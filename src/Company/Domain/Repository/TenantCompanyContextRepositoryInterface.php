<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository;

use App\Company\Domain\Entity\Company;

interface TenantCompanyContextRepositoryInterface
{
    public function findById(int $id): ?Company;
}
