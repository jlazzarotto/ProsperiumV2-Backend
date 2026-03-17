<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\Tenant\PerfilPermissao;

interface PerfilPermissaoRepositoryInterface
{
    public function save(PerfilPermissao $perfilPermissao): void;

    /**
     * @return list<string>
     */
    public function listPermissionCodesByPerfil(int $perfilId): array;

    public function deleteByPerfilId(int $perfilId): void;
}
