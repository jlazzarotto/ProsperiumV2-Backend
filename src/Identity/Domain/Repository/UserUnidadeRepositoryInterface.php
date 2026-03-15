<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\UserUnidade;

interface UserUnidadeRepositoryInterface
{
    public function save(UserUnidade $userUnidade): void;

    public function userHasUnidade(int $userId, int $companyId, int $unidadeId): bool;

    /**
     * @return list<int>
     */
    public function listUnidadeIdsByUser(int $userId, ?int $companyId = null): array;
}
