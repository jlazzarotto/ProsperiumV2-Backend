<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\UserPerfil;
use App\Identity\Domain\Repository\UserPerfilRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPerfil>
 */
final class DoctrineUserPerfilRepository extends ServiceEntityRepository implements UserPerfilRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPerfil::class);
    }

    public function save(UserPerfil $userPerfil): void
    {
        $em = $this->getEntityManager();
        $em->persist($userPerfil);
        $em->flush();
    }

    public function userHasPermission(int $userId, string $permissionCode, ?int $companyId = null, ?int $empresaId = null, ?int $unidadeId = null): bool
    {
        $qb = $this->createQueryBuilder('userPerfil')
            ->select('COUNT(userPerfil.id)')
            ->innerJoin('userPerfil.perfil', 'perfil')
            ->innerJoin('App\Identity\Domain\Entity\PerfilPermissao', 'perfilPermissao', 'ON', 'perfilPermissao.perfil = perfil')
            ->innerJoin('perfilPermissao.permissao', 'permissao')
            ->andWhere('userPerfil.user = :userId')
            ->andWhere('permissao.codigo = :permissionCode')
            ->andWhere('userPerfil.status = :status')
            ->setParameter('userId', $userId)
            ->setParameter('permissionCode', $permissionCode)
            ->setParameter('status', 'active');

        if ($companyId !== null) {
            $qb->andWhere('userPerfil.company = :companyId')
                ->setParameter('companyId', $companyId);
        }

        if ($empresaId !== null) {
            $qb->andWhere('(userPerfil.empresa IS NULL OR userPerfil.empresa = :empresaId)')
                ->setParameter('empresaId', $empresaId);
        }

        if ($unidadeId !== null) {
            $qb->andWhere('(userPerfil.unidade IS NULL OR userPerfil.unidade = :unidadeId)')
                ->setParameter('unidadeId', $unidadeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function listProfileCodesByUser(int $userId, ?int $companyId = null): array
    {
        $qb = $this->createQueryBuilder('userPerfil')
            ->select('perfil.codigo AS codigo')
            ->innerJoin('userPerfil.perfil', 'perfil')
            ->andWhere('userPerfil.user = :userId')
            ->setParameter('userId', $userId);

        if ($companyId !== null) {
            $qb->andWhere('userPerfil.company = :companyId')
                ->setParameter('companyId', $companyId);
        }

        return array_map(
            static fn (array $row): string => (string) $row['codigo'],
            $qb->getQuery()->getArrayResult()
        );
    }

    public function listPermissionCodesByUser(int $userId, ?int $companyId = null): array
    {
        $qb = $this->createQueryBuilder('userPerfil')
            ->select('DISTINCT permissao.codigo AS codigo')
            ->innerJoin('userPerfil.perfil', 'perfil')
            ->innerJoin('App\Identity\Domain\Entity\PerfilPermissao', 'perfilPermissao', 'WITH', 'perfilPermissao.perfil = perfil')
            ->innerJoin('perfilPermissao.permissao', 'permissao')
            ->andWhere('userPerfil.user = :userId')
            ->andWhere('userPerfil.status = :status')
            ->setParameter('userId', $userId)
            ->setParameter('status', 'active')
            ->orderBy('permissao.codigo', 'ASC');

        if ($companyId !== null) {
          $qb->andWhere('userPerfil.company = :companyId')
              ->setParameter('companyId', $companyId);
        }

        return array_map(
            static fn (array $row): string => (string) $row['codigo'],
            $qb->getQuery()->getArrayResult()
        );
    }

    public function deleteByUserAndCompany(int $userId, int $companyId): void
    {
        $this->getEntityManager()
            ->createQuery('DELETE FROM App\Identity\Domain\Entity\UserPerfil up WHERE up.user = :userId AND up.company = :companyId')
            ->setParameter('userId', $userId)
            ->setParameter('companyId', $companyId)
            ->execute();
    }
}
