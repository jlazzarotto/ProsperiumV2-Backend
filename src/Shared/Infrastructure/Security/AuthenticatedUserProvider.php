<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

use App\Identity\Domain\Entity\User;
use App\Shared\Domain\Contract\AuthenticatedUserProviderInterface;
use App\Shared\Domain\Exception\UnauthorizedOperationException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AuthenticatedUserProvider implements AuthenticatedUserProviderInterface
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function getUser(): ?AuthenticatedUser
    {
        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        if (!$user instanceof User || $user->getId() === null) {
            return null;
        }

        return new AuthenticatedUser((int) $user->getId(), $user->getEmail(), $user->getNome());
    }

    public function requireUser(): AuthenticatedUser
    {
        $user = $this->getUser();

        if ($user === null) {
            throw new UnauthorizedOperationException('Usuário autenticado não encontrado no contexto da requisição.');
        }

        return $user;
    }
}
