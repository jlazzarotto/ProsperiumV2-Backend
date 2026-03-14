<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

use App\Shared\Domain\Contract\AuthenticatedUserProviderInterface;

final class AuthenticatedUserProvider implements AuthenticatedUserProviderInterface
{
    public function getUser(): ?AuthenticatedUser
    {
        return null;
    }
}
