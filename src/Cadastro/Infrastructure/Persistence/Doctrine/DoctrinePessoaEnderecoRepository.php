<?php

declare(strict_types=1);

namespace App\Cadastro\Infrastructure\Persistence\Doctrine;

use App\Cadastro\Domain\Entity\PessoaEndereco;
use App\Cadastro\Domain\Repository\PessoaEnderecoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PessoaEndereco>
 */
final class DoctrinePessoaEnderecoRepository extends ServiceEntityRepository implements PessoaEnderecoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PessoaEndereco::class);
    }

    public function save(PessoaEndereco $endereco): void
    {
        $em = $this->getEntityManager();
        $em->persist($endereco);
        $em->flush();
    }

    public function findById(int $id): ?PessoaEndereco
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id = :id')
            ->andWhere('e.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function softDelete(PessoaEndereco $endereco): void
    {
        $endereco->softDelete();
        $em = $this->getEntityManager();
        $em->persist($endereco);
        $em->flush();
    }

    public function listByPessoa(int $pessoaId): array
    {
        /** @var list<PessoaEndereco> $items */
        $items = $this->createQueryBuilder('e')
            ->andWhere('e.pessoa = :pessoaId')
            ->andWhere('e.deletedAt IS NULL')
            ->setParameter('pessoaId', $pessoaId)
            ->orderBy('e.principal', 'DESC')
            ->addOrderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $items;
    }
}
