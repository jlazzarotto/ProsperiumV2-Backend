<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\Modulo;
use App\Identity\Domain\Repository\ModuloRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Modulo>
 */
final class DoctrineModuloRepository extends ServiceEntityRepository implements ModuloRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Modulo::class);
    }

    public function listAllActive(): array
    {
        /** @var list<Modulo> $items */
        $items = $this->createQueryBuilder('modulo')
            ->andWhere('modulo.status = :status')
            ->setParameter('status', 'active')
            ->orderBy('modulo.categoriaCodigo', 'ASC')
            ->addOrderBy('modulo.sortOrder', 'ASC')
            ->addOrderBy('modulo.codigo', 'ASC')
            ->getQuery()
            ->getResult();

        return $items;
    }

    public function listMenuEntries(): array
    {
        /** @var list<Modulo> $items */
        $items = $this->createQueryBuilder('modulo')
            ->andWhere('modulo.status = :status')
            ->andWhere('modulo.isMenuEntry = :isMenuEntry')
            ->andWhere('modulo.routePath IS NOT NULL')
            ->setParameter('status', 'active')
            ->setParameter('isMenuEntry', true)
            ->orderBy('modulo.categoriaCodigo', 'ASC')
            ->addOrderBy('modulo.sortOrder', 'ASC')
            ->addOrderBy('modulo.codigo', 'ASC')
            ->getQuery()
            ->getResult();

        return $items;
    }
}
