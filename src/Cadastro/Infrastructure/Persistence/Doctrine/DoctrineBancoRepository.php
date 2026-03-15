<?php

declare(strict_types=1);

namespace App\Cadastro\Infrastructure\Persistence\Doctrine;

use App\Cadastro\Domain\Entity\Banco;
use App\Cadastro\Domain\Repository\BancoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Banco> */
final class DoctrineBancoRepository extends ServiceEntityRepository implements BancoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banco::class);
    }

    public function findById(int $id): ?Banco
    {
        return $this->find($id);
    }

    public function listAll(?string $status = null): array
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.nome', 'ASC');

        if ($status !== null) {
            $qb->andWhere('b.status = :status')->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }
}
