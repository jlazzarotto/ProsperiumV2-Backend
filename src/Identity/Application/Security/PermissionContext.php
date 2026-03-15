<?php

declare(strict_types=1);

namespace App\Identity\Application\Security;

final class PermissionContext
{
    public function __construct(
        public readonly string $permissionCode,
        public readonly ?int $companyId = null,
        public readonly ?int $empresaId = null,
        public readonly ?int $unidadeId = null
    ) {
    }
}
