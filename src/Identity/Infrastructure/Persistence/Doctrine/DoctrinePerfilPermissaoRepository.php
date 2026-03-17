<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\Tenant\PerfilPermissao;
use App\Identity\Domain\Repository\PerfilPermissaoRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @extends EntityRepository<PerfilPermissao>
 */
final class DoctrinePerfilPermissaoRepository extends EntityRepository implements PerfilPermissaoRepositoryInterface
{
    public function __construct(
        #[Autowire(service: 'doctrine.orm.tenant_entity_manager')]
        EntityManagerInterface $em
    ) {
        parent::__construct($em, $em->getClassMetadata(PerfilPermissao::class));
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
            ->select('perfilPermissao.permissaoId AS permissaoId')
            ->andWhere('perfilPermissao.perfil = :perfilId')
            ->setParameter('perfilId', $perfilId)
            ->getQuery()
            ->getArrayResult();

        return array_map(static fn (array $row): string => (string) $row['permissaoId'], $rows);
    }

    public function deleteByPerfilId(int $perfilId): void
    {
        $this->getEntityManager()
            ->createQuery('DELETE FROM App\Identity\Domain\Entity\Tenant\PerfilPermissao pap WHERE pap.perfil = :perfilId')
            ->setParameter('perfilId', $perfilId)
            ->execute();
    }
}
