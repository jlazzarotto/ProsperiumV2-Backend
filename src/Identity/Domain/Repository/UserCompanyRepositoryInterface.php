<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\UserCompany;

interface UserCompanyRepositoryInterface
{
    public function save(UserCompany $userCompany): void;

    public function userHasCompany(int $userId, int $companyId): bool;

    public function isCompanyAdmin(int $userId, int $companyId): bool;

    /**
     * @return list<int>
     */
    public function listCompanyIdsByUser(int $userId): array;
}
