<?php

declare(strict_types=1);

namespace App\Cadastro\Infrastructure\Persistence\Doctrine;

use App\Cadastro\Domain\Entity\Referencia\Uf;
use App\Cadastro\Domain\Repository\UfRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/** @extends EntityRepository<Uf> */
final class DoctrineUfRepository extends EntityRepository implements UfRepositoryInterface
{
    public function __construct(
        #[Autowire(service: 'doctrine.orm.control_entity_manager')]
        EntityManagerInterface $em
    ) {
        parent::__construct($em, $em->getClassMetadata(Uf::class));
    }

    public function findByCodigoIbge(int $codigoIbge): ?Uf
    {
        return $this->findOneBy(['codigoIbge' => $codigoIbge]);
    }

    public function listAll(): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.sigla', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function listFiltered(?string $query = null, ?string $sigla = null, ?string $status = 'active', int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.sigla', 'ASC')
            ->setMaxResults(max(1, min($limit, 100)));

        if ($query !== null && trim($query) !== '') {
            $q = mb_strtolower(trim($query));
            $qb->andWhere('LOWER(u.nome) LIKE :q OR LOWER(u.sigla) LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        if ($sigla !== null && trim($sigla) !== '') {
            $qb->andWhere('u.sigla = :sigla')->setParameter('sigla', mb_strtoupper(trim($sigla)));
        }

        if ($status !== null && $status !== '') {
            $qb->andWhere('u.status = :status')->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }
}
