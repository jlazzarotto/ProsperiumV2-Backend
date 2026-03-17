<?php

declare(strict_types=1);

namespace App\Cadastro\Infrastructure\Persistence\Doctrine;

use App\Cadastro\Domain\Entity\Referencia\Municipio;
use App\Cadastro\Domain\Repository\MunicipioRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/** @extends EntityRepository<Municipio> */
final class DoctrineMunicipioRepository extends EntityRepository implements MunicipioRepositoryInterface
{
    public function __construct(
        #[Autowire(service: 'doctrine.orm.control_entity_manager')]
        EntityManagerInterface $em
    ) {
        parent::__construct($em, $em->getClassMetadata(Municipio::class));
    }

    public function save(Municipio $municipio): void
    {
        $em = $this->getEntityManager();
        $em->persist($municipio);
        $em->flush();
    }

    public function findByCodigoIbge(int $codigoIbge): ?Municipio
    {
        return $this->findOneBy(['codigoIbge' => $codigoIbge]);
    }

    public function listFiltered(?string $ufSigla = null, ?string $query = null, ?int $codigoIbge = null, ?string $status = 'active', int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('m')
            ->orderBy('m.ufSigla', 'ASC')
            ->addOrderBy('m.nome', 'ASC')
            ->setMaxResults(max(1, min($limit, 500)));

        if ($codigoIbge !== null) {
            $qb->andWhere('m.codigoIbge = :codigoIbge')->setParameter('codigoIbge', $codigoIbge);
        }

        if ($ufSigla !== null && $ufSigla !== '') {
            $qb->andWhere('m.ufSigla = :ufSigla')->setParameter('ufSigla', mb_strtoupper($ufSigla));
        }

        if ($query !== null && trim($query) !== '') {
            $qb->andWhere('LOWER(m.nome) LIKE :query')
                ->setParameter('query', '%' . mb_strtolower(trim($query)) . '%');
        }

        if ($status !== null && $status !== '') {
            $qb->andWhere('m.status = :status')->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    public function listAll(): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.ufSigla', 'ASC')
            ->addOrderBy('m.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
