<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\Permissao;
use App\Identity\Domain\Repository\PermissaoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Permissao>
 */
final class DoctrinePermissaoRepository extends ServiceEntityRepository implements PermissaoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permissao::class);
    }

    public function findByCodigo(string $codigo): ?Permissao
    {
        return $this->findOneBy(['codigo' => trim($codigo)]);
    }

    public function findByCodigos(array $codigos): array
    {
        if ($codigos === []) {
            return [];
        }

        /** @var list<Permissao> $items */
        $items = $this->createQueryBuilder('permissao')
            ->andWhere('permissao.codigo IN (:codigos)')
            ->setParameter('codigos', array_values(array_unique($codigos)))
            ->orderBy('permissao.codigo', 'ASC')
            ->getQuery()
            ->getResult();

        return $items;
    }

    public function listAll(?string $moduloCodigo = null): array
    {
        $qb = $this->createQueryBuilder('permissao')
            ->innerJoin('permissao.modulo', 'modulo')
            ->addSelect('modulo')
            ->orderBy('permissao.codigo', 'ASC');

        if ($moduloCodigo !== null) {
            $qb
                ->andWhere('modulo.codigo = :moduloCodigo')
                ->setParameter('moduloCodigo', $moduloCodigo);
        }

        /** @var list<Permissao> $items */
        $items = $qb->getQuery()->getResult();

        return $items;
    }
}
