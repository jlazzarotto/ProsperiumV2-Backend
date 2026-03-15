<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

final class AuthenticatedUser
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $nome
    ) {
    }
}
