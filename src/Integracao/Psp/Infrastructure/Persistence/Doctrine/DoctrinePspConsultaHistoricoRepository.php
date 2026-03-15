<?php

declare(strict_types=1);

namespace App\Integracao\Psp\Infrastructure\Persistence\Doctrine;

use App\Integracao\Psp\Domain\Entity\PspConsultaHistorico;
use App\Integracao\Psp\Domain\Repository\PspConsultaHistoricoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<PspConsultaHistorico> */
final class DoctrinePspConsultaHistoricoRepository extends ServiceEntityRepository implements PspConsultaHistoricoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PspConsultaHistorico::class);
    }

    public function save(PspConsultaHistorico $historico): void
    {
        $em = $this->getEntityManager();
        $em->persist($historico);
        $em->flush();
    }

    public function listRecent(?int $companyId = null, ?string $endpointKey = null, int $limit = 30): array
    {
        $qb = $this->createQueryBuilder('h')
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults($limit);

        if ($companyId !== null) {
            $qb->andWhere('h.companyId = :companyId')->setParameter('companyId', $companyId);
        }

        if ($endpointKey !== null && trim($endpointKey) !== '') {
            $qb->andWhere('h.endpointKey = :endpointKey')->setParameter('endpointKey', trim($endpointKey));
        }

        return $qb->getQuery()->getResult();
    }
}
