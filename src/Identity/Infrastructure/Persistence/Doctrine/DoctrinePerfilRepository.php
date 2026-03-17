<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\Tenant\Perfil;
use App\Identity\Domain\Repository\PerfilRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @extends EntityRepository<Perfil>
 */
final class DoctrinePerfilRepository extends EntityRepository implements PerfilRepositoryInterface
{
    public function __construct(
        #[Autowire(service: 'doctrine.orm.tenant_entity_manager')]
        EntityManagerInterface $em
    ) {
        parent::__construct($em, $em->getClassMetadata(Perfil::class));
    }

    public function save(Perfil $perfil): void
    {
        $em = $this->getEntityManager();
        $em->persist($perfil);
        $em->flush();
    }

    public function findById(int $id): ?Perfil
    {
        return $this->find($id);
    }

    public function findByCodigo(string $codigo, ?int $companyId = null): ?Perfil
    {
        if ($companyId !== null) {
            $companyProfile = $this->findOneBy([
                'codigo' => trim($codigo),
                'companyId' => $companyId,
            ]);

            if ($companyProfile !== null) {
                return $companyProfile;
            }
        }

        return $this->findOneBy([
            'codigo' => trim($codigo),
            'companyId' => null,
        ]);
    }

    public function findByCodigos(array $codigos, ?int $companyId = null): array
    {
        if ($codigos === []) {
            return [];
        }

        $qb = $this->createQueryBuilder('perfil')
            ->andWhere('perfil.codigo IN (:codigos)')
            ->setParameter('codigos', array_values(array_unique($codigos)));

        if ($companyId === null) {
            $qb->andWhere('perfil.companyId IS NULL');
        } else {
            $qb->andWhere('(perfil.companyId = :companyId OR perfil.companyId IS NULL)')
                ->setParameter('companyId', $companyId);
        }

        /** @var list<Perfil> $items */
        $items = $qb->getQuery()->getResult();

        return $items;
    }

    public function listAll(?int $companyId = null): array
    {
        $qb = $this->createQueryBuilder('perfil')
            ->orderBy('perfil.tipo', 'ASC')
            ->addOrderBy('perfil.nome', 'ASC');

        if ($companyId === null) {
            $qb->andWhere('perfil.companyId IS NULL');
        } else {
            $qb->andWhere('(perfil.companyId = :companyId OR perfil.companyId IS NULL)')
                ->setParameter('companyId', $companyId);
        }

        /** @var list<Perfil> $items */
        $items = $qb->getQuery()->getResult();

        return $items;
    }
}
