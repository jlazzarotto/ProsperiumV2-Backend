<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence\Doctrine;

use App\Company\Domain\Entity\UnidadeNegocio;
use App\Company\Domain\Repository\TenantUnidadeNegocioContextRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTenantUnidadeNegocioContextRepository implements TenantUnidadeNegocioContextRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $tenantEntityManager)
    {
    }

    public function findById(int $id): ?UnidadeNegocio
    {
        return $this->tenantEntityManager->find(UnidadeNegocio::class, $id);
    }
}
