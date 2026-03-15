<?php

declare(strict_types=1);

namespace App\Cadastro\Infrastructure\Persistence\Doctrine;

use App\Cadastro\Domain\Entity\Uf;
use App\Cadastro\Domain\Repository\UfRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Uf> */
final class DoctrineUfRepository extends ServiceEntityRepository implements UfRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Uf::class);
    }

    public function findByCodigoIbge(int $codigoIbge): ?Uf
    {
        return $this->findOneBy(['codigoIbge' => $codigoIbge]);
    }

    public function listAll(): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.sigla', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function listFiltered(?string $query = null, ?string $sigla = null, ?string $status = 'active', int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.sigla', 'ASC')
            ->setMaxResults(max(1, min($limit, 100)));

        if ($query !== null && trim($query) !== '') {
            $q = mb_strtolower(trim($query));
            $qb->andWhere('LOWER(u.nome) LIKE :q OR LOWER(u.sigla) LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        if ($sigla !== null && trim($sigla) !== '') {
            $qb->andWhere('u.sigla = :sigla')->setParameter('sigla', mb_strtoupper(trim($sigla)));
        }

        if ($status !== null && $status !== '') {
            $qb->andWhere('u.status = :status')->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }
}
