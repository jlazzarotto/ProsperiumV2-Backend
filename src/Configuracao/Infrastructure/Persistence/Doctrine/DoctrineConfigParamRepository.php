<?php

declare(strict_types=1);

namespace App\Configuracao\Infrastructure\Persistence\Doctrine;

use App\Company\Domain\Entity\Company;
use App\Configuracao\Domain\Entity\ConfigParam;
use App\Configuracao\Domain\Repository\ConfigParamRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ConfigParam> */
final class DoctrineConfigParamRepository extends ServiceEntityRepository implements ConfigParamRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfigParam::class);
    }

    public function save(ConfigParam $param): void
    {
        $em = $this->getEntityManager();
        $em->persist($param);
        $em->flush();
    }

    public function findById(int $id): ?ConfigParam
    {
        return $this->find($id);
    }

    public function findByCompanyAndName(int $companyId, string $name): ?ConfigParam
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.company = :companyId')
            ->andWhere('p.name = :name')
            ->setParameter('companyId', $companyId)
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function listAll(int $companyId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.company = :companyId')
            ->setParameter('companyId', $companyId)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function listDistinctTypes(int $companyId): array
    {
        $rows = $this->createQueryBuilder('p')
            ->select('DISTINCT p.type')
            ->andWhere('p.company = :companyId')
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
        return $this->getEntityManager()->getReference(Company::class, $companyId);
    }
}
