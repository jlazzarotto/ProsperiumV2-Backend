<?php

declare(strict_types=1);

namespace App\Cadastro\Infrastructure\Persistence\Doctrine;

use App\Cadastro\Domain\Entity\Pessoa;
use App\Cadastro\Domain\Repository\PessoaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pessoa>
 */
final class DoctrinePessoaRepository extends ServiceEntityRepository implements PessoaRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pessoa::class);
    }

    public function save(Pessoa $pessoa): void
    {
        $em = $this->getEntityManager();
        $em->persist($pessoa);
        $em->flush();
    }

    public function findById(int $id): ?Pessoa
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByCompanyAndDocumento(int $companyId, string $documento, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.company = :companyId')
            ->andWhere('p.documento = :documento')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('companyId', $companyId)
            ->setParameter('documento', $documento);

        if ($excludeId !== null) {
            $qb->andWhere('p.id != :excludeId')->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function softDelete(Pessoa $pessoa): void
    {
        $pessoa->softDelete();
        $em = $this->getEntityManager();
        $em->persist($pessoa);
        $em->flush();
    }

    public function listAll(int $companyId, ?string $tipoPessoa = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.company = :companyId')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('companyId', $companyId)
            ->orderBy('p.nomeRazao', 'ASC');

        if ($tipoPessoa !== null) {
            $qb->andWhere('p.tipoPessoa = :tipoPessoa')->setParameter('tipoPessoa', $tipoPessoa);
        }

        if ($status !== null) {
            $qb->andWhere('p.status = :status')->setParameter('status', $status);
        }

        /** @var list<Pessoa> $items */
        $items = $qb->getQuery()->getResult();

        return $items;
    }
}
