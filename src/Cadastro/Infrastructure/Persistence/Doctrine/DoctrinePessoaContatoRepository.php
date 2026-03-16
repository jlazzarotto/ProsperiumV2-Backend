<?php

declare(strict_types=1);

namespace App\Cadastro\Infrastructure\Persistence\Doctrine;

use App\Cadastro\Domain\Entity\PessoaContato;
use App\Cadastro\Domain\Repository\PessoaContatoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PessoaContato>
 */
final class DoctrinePessoaContatoRepository extends ServiceEntityRepository implements PessoaContatoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PessoaContato::class);
    }

    public function save(PessoaContato $contato): void
    {
        $em = $this->getEntityManager();
        $em->persist($contato);
        $em->flush();
    }

    public function findById(int $id): ?PessoaContato
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function softDelete(PessoaContato $contato): void
    {
        $contato->softDelete();
        $em = $this->getEntityManager();
        $em->persist($contato);
        $em->flush();
    }

    public function listByPessoa(int $pessoaId): array
    {
        /** @var list<PessoaContato> $items */
        $items = $this->createQueryBuilder('c')
            ->andWhere('c.pessoa = :pessoaId')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('pessoaId', $pessoaId)
            ->orderBy('c.principal', 'DESC')
            ->addOrderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $items;
    }
}
