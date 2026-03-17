<?php

declare(strict_types=1);

namespace App\Configuracao\Infrastructure\Persistence\Doctrine;

use App\Company\Domain\Entity\Company;
use App\Configuracao\Domain\Entity\ConfigParam;
use App\Configuracao\Domain\Repository\ConfigParamRepositoryInterface;
use App\Shared\Domain\Contract\TenantEntityManagerProviderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ConfigParam> */
final class DoctrineConfigParamRepository extends ServiceEntityRepository implements ConfigParamRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly TenantEntityManagerProviderInterface $tenantEntityManagerProvider
    ) {
        parent::__construct($registry, ConfigParam::class);
    }

    public function save(ConfigParam $param): void
    {
        $em = $this->tenantEntityManagerProvider->getEntityManager();
        $em->persist($param);
        $em->flush();
    }

    public function findById(int $id): ?ConfigParam
    {
        $em = $this->tenantEntityManagerProvider->getEntityManager();
        return $em->getRepository(ConfigParam::class)->find($id);
    }

    public function findByCompanyAndName(int $companyId, string $name): ?ConfigParam
    {
        $em = $this->tenantEntityManagerProvider->getEntityManager();
        $repo = $em->getRepository(ConfigParam::class);

        return $repo->createQueryBuilder('p')
            ->andWhere('p.companyId = :companyId')
            ->andWhere('p.name = :name')
            ->setParameter('companyId', $companyId)
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function listAll(int $companyId): array
    {
        $em = $this->tenantEntityManagerProvider->getEntityManager();
        $repo = $em->getRepository(ConfigParam::class);

        return $repo->createQueryBuilder('p')
            ->andWhere('p.companyId = :companyId')
            ->setParameter('companyId', $companyId)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function listDistinctTypes(int $companyId): array
    {
        $em = $this->tenantEntityManagerProvider->getEntityManager();
        $repo = $em->getRepository(ConfigParam::class);

        $rows = $repo->createQueryBuilder('p')
            ->select('DISTINCT p.type')
            ->andWhere('p.companyId = :companyId')
            ->andWhere('p.type IS NOT NULL')
            ->andWhere("TRIM(p.type) != ''")
            ->setParameter('companyId', $companyId)
            ->orderBy('p.type', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();

        return array_values(array_filter(array_map(
            static fn($type) => trim((string) $type),
            $rows
        )));
    }

    public function getCompanyReference(int $companyId): Company
    {
        return $this->tenantEntityManagerProvider->getEntityManager()->getReference(Company::class, $companyId);
    }
}
