<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\UserAlcada;
use App\Identity\Domain\Repository\UserAlcadaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAlcada>
 */
final class DoctrineUserAlcadaRepository extends ServiceEntityRepository implements UserAlcadaRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAlcada::class);
    }

    public function userHasActiveAlcadaForValue(int $userId, int $companyId, ?int $empresaId, ?int $unidadeId, string $tipoOperacao, string $valor): bool
    {
        $qb = $this->createQueryBuilder('ua')
            ->select('COUNT(ua.id)')
            ->andWhere('ua.user = :userId')
            ->andWhere('ua.company = :companyId')
            ->andWhere('ua.status = :status')
            ->andWhere('ua.tipoOperacao = :tipoOperacao')
            ->andWhere('(ua.empresa IS NULL OR ua.empresa = :empresaId)')
            ->andWhere('(ua.unidade IS NULL OR ua.unidade = :unidadeId)')
            ->andWhere('(ua.valorLimite IS NULL OR ua.valorLimite >= :valor)')
            ->setParameter('userId', $userId)
            ->setParameter('companyId', $companyId)
            ->setParameter('status', 'active')
            ->setParameter('tipoOperacao', $tipoOperacao)
            ->setParameter('empresaId', $empresaId)
            ->setParameter('unidadeId', $unidadeId)
            ->setParameter('valor', $valor);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
