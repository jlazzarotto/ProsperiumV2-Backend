<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence\Doctrine;

use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Repository\TenantEmpresaContextRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTenantEmpresaContextRepository implements TenantEmpresaContextRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $tenantEntityManager)
    {
    }

    public function findById(int $id): ?Empresa
    {
        return $this->tenantEntityManager->find(Empresa::class, $id);
    }
}
