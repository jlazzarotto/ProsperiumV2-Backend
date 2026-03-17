<?php

declare(strict_types=1);

namespace App\Identity\Application\Service;

use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class LoginService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly AuditoriaLogger $auditoriaLogger
    ) {
    }

    /**
     * Valida credenciais do usuário conforme UC-002.
     * Retorna o User se credenciais válidas, ou lança exceção.
     */
    public function validateCredentials(string $email, string $password): User
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            $this->auditoriaLogger->log(
                null,
                'auth',
                'identity.login.email_not_found',
                ['email' => $email]
            );

            throw new ValidationException([
                'credentials' => ['Credenciais invalidas.'],
            ]);
        }

        // Verificar se está bloqueado
        if ($user->isBloqueado()) {
            if ($user->isLocked()) {
                $this->auditoriaLogger->log(
                    $user->getCompany()?->getId() !== null ? (int) $user->getCompanyId() : null,
                    'auth',
                    'identity.login.account_locked',
                    [
                        'userId' => $user->getId(),
                        'email' => $user->getEmail(),
                        'lockedUntil' => $user->getLockedUntil()?->format('Y-m-d H:i:s'),
                    ]
                );

                throw new ValidationException([
                    'credentials' => ['Conta bloqueada. Tente novamente em alguns minutos ou contacte o administrador.'],
                ]);
            }

            // Lock expirou, desbloquear automaticamente
            $user->desbloquear();
            $this->userRepository->save($user);
        }

        // Verificar se está inativo
        if ($user->getStatus() === User::STATUS_INATIVO) {
            throw new ValidationException([
                'credentials' => ['Conta inativa. Contacte o administrador.'],
            ]);
        }

        // Validar senha
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            $user->registerFailedLogin();
            $this->userRepository->save($user);

            $this->auditoriaLogger->log(
                $user->getCompany()?->getId() !== null ? (int) $user->getCompanyId() : null,
                'auth',
                'identity.login.invalid_password',
                [
                    'userId' => $user->getId(),
                    'email' => $user->getEmail(),
                    'failedAttempts' => $user->getFailedLoginAttempts(),
                    'bloqueado' => $user->isBloqueado(),
                ]
            );

            $remainingAttempts = User::MAX_FAILED_ATTEMPTS - $user->getFailedLoginAttempts();
            if ($remainingAttempts > 0) {
                throw new ValidationException([
                    'credentials' => ["Credenciais invalidas. {$remainingAttempts} tentativa(s) restante(s)."],
                ]);
            }

            throw new ValidationException([
                'credentials' => ['Conta bloqueada apos 5 tentativas. Tente novamente em 15 minutos.'],
            ]);
        }

        // Login bem-sucedido
        $user->registerSuccessfulLogin();
        $this->userRepository->save($user);

        $this->auditoriaLogger->log(
            $user->getCompany()?->getId() !== null ? (int) $user->getCompanyId() : null,
            'auth',
            'identity.login.success',
            [
                'userId' => $user->getId(),
                'email' => $user->getEmail(),
            ]
        );

        return $user;
    }
}
