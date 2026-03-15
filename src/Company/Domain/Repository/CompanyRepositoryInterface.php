<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository;

use App\Company\Domain\Entity\Company;

interface CompanyRepositoryInterface
{
    public function save(Company $company): void;

    public function findById(int $id): ?Company;

    public function countAll(): int;

    /**
     * @return list<Company>
     */
    public function listAll(?string $status = null): array;
}
