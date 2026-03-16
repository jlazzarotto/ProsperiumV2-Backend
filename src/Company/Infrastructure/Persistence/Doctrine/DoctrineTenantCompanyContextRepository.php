<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence\Doctrine;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Repository\TenantCompanyContextRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTenantCompanyContextRepository implements TenantCompanyContextRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $tenantEntityManager)
    {
    }

    public function findById(int $id): ?Company
    {
        return $this->tenantEntityManager->find(Company::class, $id);
    }
}
