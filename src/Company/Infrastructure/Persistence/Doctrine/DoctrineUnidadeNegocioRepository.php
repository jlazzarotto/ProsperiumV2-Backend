<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence\Doctrine;

use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @extends EntityRepository<UnidadeNegocio>
 */
final class DoctrineUnidadeNegocioRepository extends EntityRepository implements UnidadeNegocioRepositoryInterface
{
    public function __construct(
        #[Autowire(service: 'doctrine.orm.tenant_entity_manager')]
        EntityManagerInterface $em
    ) {
        parent::__construct($em, $em->getClassMetadata(UnidadeNegocio::class));
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
            'companyId' => $companyId,
            'nome' => trim($nome),
        ]) > 0;
    }

    public function existsByCompanyAndAbreviatura(int $companyId, string $abreviatura): bool
    {
        return $this->count([
            'companyId' => $companyId,
            'abreviatura' => trim($abreviatura),
        ]) > 0;
    }

    public function listAll(?int $companyId = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('unidade')
            ->orderBy('unidade.id', 'ASC');

        if ($companyId !== null) {
            $qb
                ->andWhere('unidade.companyId = :companyId')
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
