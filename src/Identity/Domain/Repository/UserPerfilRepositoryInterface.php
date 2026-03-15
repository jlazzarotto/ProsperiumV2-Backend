<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\UserPerfil;

interface UserPerfilRepositoryInterface
{
    public function save(UserPerfil $userPerfil): void;

    public function userHasPermission(int $userId, string $permissionCode, ?int $companyId = null, ?int $empresaId = null, ?int $unidadeId = null): bool;

    /**
     * @return list<string>
     */
    public function listProfileCodesByUser(int $userId, ?int $companyId = null): array;

    /**
     * @return list<string>
     */
    public function listPermissionCodesByUser(int $userId, ?int $companyId = null): array;

    public function deleteByUserAndCompany(int $userId, int $companyId): void;
}
