<?php

declare(strict_types=1);

namespace App\Cadastro\Infrastructure\Persistence\Doctrine;

use App\Cadastro\Domain\Entity\Pais;
use App\Cadastro\Domain\Repository\PaisRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Pais> */
final class DoctrinePaisRepository extends ServiceEntityRepository implements PaisRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pais::class);
    }

    public function findByCodigoM49(int $codigoM49): ?Pais
    {
        return $this->findOneBy(['codigoM49' => $codigoM49]);
    }

    public function listAll(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function listFiltered(?string $query = null, ?string $status = 'active', int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.nome', 'ASC')
            ->setMaxResults(max(1, min($limit, 500)));

        if ($query !== null && trim($query) !== '') {
            $q = mb_strtolower(trim($query));
            $qb->andWhere('LOWER(p.nome) LIKE :q OR LOWER(p.isoAlpha2) LIKE :q OR LOWER(p.isoAlpha3) LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        if ($status !== null && $status !== '') {
            $qb->andWhere('p.status = :status')->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }
}
