<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\Perfil;
use App\Identity\Domain\Repository\PerfilRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Perfil>
 */
final class DoctrinePerfilRepository extends ServiceEntityRepository implements PerfilRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Perfil::class);
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
                'company' => $companyId,
            ]);

            if ($companyProfile !== null) {
                return $companyProfile;
            }
        }

        return $this->findOneBy([
            'codigo' => trim($codigo),
            'company' => null,
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
            $qb->andWhere('perfil.company IS NULL');
        } else {
            $qb->andWhere('(perfil.company = :companyId OR perfil.company IS NULL)')
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
            $qb->andWhere('perfil.company IS NULL');
        } else {
            $qb->andWhere('(perfil.company = :companyId OR perfil.company IS NULL)')
                ->setParameter('companyId', $companyId);
        }

        /** @var list<Perfil> $items */
        $items = $qb->getQuery()->getResult();

        return $items;
    }
}
