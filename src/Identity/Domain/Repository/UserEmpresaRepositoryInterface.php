<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\UserEmpresa;

interface UserEmpresaRepositoryInterface
{
    public function save(UserEmpresa $userEmpresa): void;

    public function userHasEmpresa(int $userId, int $companyId, int $empresaId): bool;

    /**
     * @return list<int>
     */
    public function listEmpresaIdsByUser(int $userId, ?int $companyId = null): array;
}
