<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence\Doctrine;

use App\Company\Domain\Entity\TenantInstance;
use App\Company\Domain\Repository\TenantInstanceRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TenantInstance>
 */
final class DoctrineTenantInstanceRepository extends ServiceEntityRepository implements TenantInstanceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TenantInstance::class);
    }

    public function save(TenantInstance $tenantInstance): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($tenantInstance);
        $entityManager->flush();
    }

    public function findByCompanyId(int $companyId): ?TenantInstance
    {
        return $this->findOneBy(['company' => $companyId]);
    }

    public function existsByDatabaseKey(string $databaseKey): bool
    {
        return $this->count(['databaseKey' => trim($databaseKey)]) > 0;
    }

    public function findByDatabaseKey(string $databaseKey): ?TenantInstance
    {
        return $this->findOneBy(['databaseKey' => trim($databaseKey)]);
    }

    /**
     * @return list<TenantInstance>
     */
    public function findAllActive(): array
    {
        return $this->findBy(['status' => 'active'], ['databaseKey' => 'ASC']);
    }
}
