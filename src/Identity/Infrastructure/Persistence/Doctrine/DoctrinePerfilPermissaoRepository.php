<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\PerfilPermissao;
use App\Identity\Domain\Repository\PerfilPermissaoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PerfilPermissao>
 */
final class DoctrinePerfilPermissaoRepository extends ServiceEntityRepository implements PerfilPermissaoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PerfilPermissao::class);
    }

    public function save(PerfilPermissao $perfilPermissao): void
    {
        $em = $this->getEntityManager();
        $em->persist($perfilPermissao);
        $em->flush();
    }

    public function listPermissionCodesByPerfil(int $perfilId): array
    {
        $rows = $this->createQueryBuilder('perfilPermissao')
            ->select('permissao.codigo AS codigo')
            ->innerJoin('perfilPermissao.permissao', 'permissao')
            ->andWhere('perfilPermissao.perfil = :perfilId')
            ->setParameter('perfilId', $perfilId)
            ->orderBy('permissao.codigo', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_map(static fn (array $row): string => (string) $row['codigo'], $rows);
    }

    public function deleteByPerfilId(int $perfilId): void
    {
        $this->getEntityManager()
            ->createQuery('DELETE FROM App\Identity\Domain\Entity\PerfilPermissao pap WHERE pap.perfil = :perfilId')
            ->setParameter('perfilId', $perfilId)
            ->execute();
    }
}
