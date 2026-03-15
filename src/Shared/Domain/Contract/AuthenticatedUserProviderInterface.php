<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contract;

use App\Shared\Infrastructure\Security\AuthenticatedUser;

interface AuthenticatedUserProviderInterface
{
    public function getUser(): ?AuthenticatedUser;

    public function requireUser(): AuthenticatedUser;
}
