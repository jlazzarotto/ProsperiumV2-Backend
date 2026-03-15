<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence\Doctrine;

use App\Company\Domain\Entity\UnidadeNegocio;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UnidadeNegocio>
 */
final class DoctrineUnidadeNegocioRepository extends ServiceEntityRepository implements UnidadeNegocioRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnidadeNegocio::class);
    }

    public function save(UnidadeNegocio $unidadeNegocio): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($unidadeNegocio);
        $entityManager->flush();
    }

    public function findById(int $id): ?UnidadeNegocio
    {
        return $this->find($id);
    }

    public function existsByCompanyAndNome(int $companyId, string $nome): bool
    {
        return $this->count([
            'company' => $companyId,
            'nome' => trim($nome),
        ]) > 0;
    }

    public function existsByCompanyAndAbreviatura(int $companyId, string $abreviatura): bool
    {
        return $this->count([
            'company' => $companyId,
            'abreviatura' => trim($abreviatura),
        ]) > 0;
    }

    public function listAll(?int $companyId = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('unidade')
            ->orderBy('unidade.id', 'ASC');

        if ($companyId !== null) {
            $qb
                ->andWhere('unidade.company = :companyId')
                ->setParameter('companyId', $companyId);
        }

        if ($status !== null) {
            $qb
                ->andWhere('unidade.status = :status')
                ->setParameter('status', $status);
        }

        /** @var list<UnidadeNegocio> $items */
        $items = $qb->getQuery()->getResult();

        return $items;
    }
}
