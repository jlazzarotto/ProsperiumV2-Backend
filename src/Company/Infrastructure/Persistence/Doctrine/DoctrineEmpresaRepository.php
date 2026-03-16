<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence\Doctrine;

use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Empresa>
 */
final class DoctrineEmpresaRepository extends ServiceEntityRepository implements EmpresaRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Empresa::class);
    }

    public function save(Empresa $empresa): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($empresa);
        $entityManager->flush();
    }

    public function findById(int $id): ?Empresa
    {
        return $this->createQueryBuilder('empresa')
            ->andWhere('empresa.id = :id')
            ->andWhere('empresa.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByCompanyAndCnpj(int $companyId, string $cnpj, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('empresa')
            ->select('COUNT(empresa.id)')
            ->andWhere('empresa.company = :companyId')
            ->andWhere('empresa.cnpj = :cnpj')
            ->andWhere('empresa.deletedAt IS NULL')
            ->setParameter('companyId', $companyId)
            ->setParameter('cnpj', $cnpj);

        if ($excludeId !== null) {
            $qb->andWhere('empresa.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function softDelete(Empresa $empresa): void
    {
        $empresa->softDelete();
        $entityManager = $this->getEntityManager();
        $entityManager->persist($empresa);
        $entityManager->flush();
    }

    public function listAll(?int $companyId = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('empresa')
            ->andWhere('empresa.deletedAt IS NULL')
            ->orderBy('empresa.id', 'ASC');

        if ($companyId !== null) {
            $qb
                ->andWhere('empresa.company = :companyId')
                ->setParameter('companyId', $companyId);
        }

        if ($status !== null) {
            $qb
                ->andWhere('empresa.status = :status')
                ->setParameter('status', $status);
        }

        /** @var list<Empresa> $items */
        $items = $qb->getQuery()->getResult();

        return $items;
    }
}
