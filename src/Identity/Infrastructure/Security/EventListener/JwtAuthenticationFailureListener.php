<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Security\EventListener;

use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

final class JwtAuthenticationFailureListener
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly AuditoriaLogger $auditoriaLogger,
        private readonly RequestStack $requestStack
    ) {
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return;
        }

        $content = $request->getContent();
        if ($content === '') {
            return;
        }

        try {
            $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return;
        }

        $email = $payload['email'] ?? null;
        if (!is_string($email) || $email === '') {
            return;
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            $this->auditoriaLogger->log(
                null,
                'auth',
                'identity.login.email_not_found',
                ['email' => $email]
            );

            return;
        }

        // Verificar se está bloqueado
        if ($user->isBloqueado() && $user->isLocked()) {
            $event->setResponse(new JsonResponse([
                'success' => false,
                'error' => [
                    'message' => 'Conta bloqueada. Tente novamente em alguns minutos ou contacte o administrador.',
                    'code' => 'ACCOUNT_LOCKED',
                ],
            ], JsonResponse::HTTP_FORBIDDEN));

            return;
        }

        // Verificar se está inativo
        if ($user->getStatus() === User::STATUS_INATIVO) {
            $event->setResponse(new JsonResponse([
                'success' => false,
                'error' => [
                    'message' => 'Conta inativa. Contacte o administrador.',
                    'code' => 'ACCOUNT_INACTIVE',
                ],
            ], JsonResponse::HTTP_FORBIDDEN));

            return;
        }

        // Registrar falha
        $user->registerFailedLogin();
        $this->userRepository->save($user);

        $this->auditoriaLogger->log(
            $user->getCompany()?->getId() !== null ? (int) $user->getCompany()->getId() : null,
            'auth',
            'identity.login.failed',
            [
                'userId' => $user->getId(),
                'email' => $user->getEmail(),
                'failedAttempts' => $user->getFailedLoginAttempts(),
                'bloqueado' => $user->isBloqueado(),
            ]
        );

        $remainingAttempts = User::MAX_FAILED_ATTEMPTS - $user->getFailedLoginAttempts();

        if ($user->isBloqueado()) {
            $event->setResponse(new JsonResponse([
                'success' => false,
                'error' => [
                    'message' => 'Conta bloqueada apos 5 tentativas. Tente novamente em 15 minutos.',
                    'code' => 'ACCOUNT_LOCKED',
                ],
            ], JsonResponse::HTTP_FORBIDDEN));
        } else {
            $event->setResponse(new JsonResponse([
                'success' => false,
                'error' => [
                    'message' => "Credenciais invalidas. {$remainingAttempts} tentativa(s) restante(s).",
                    'code' => 'INVALID_CREDENTIALS',
                ],
            ], JsonResponse::HTTP_UNAUTHORIZED));
        }
    }
}
