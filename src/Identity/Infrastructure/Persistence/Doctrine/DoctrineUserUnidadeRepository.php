<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\UserUnidade;
use App\Identity\Domain\Repository\UserUnidadeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserUnidade>
 */
final class DoctrineUserUnidadeRepository extends ServiceEntityRepository implements UserUnidadeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserUnidade::class);
    }

    public function save(UserUnidade $userUnidade): void
    {
        $em = $this->getEntityManager();
        $em->persist($userUnidade);
        $em->flush();
    }

    public function userHasUnidade(int $userId, int $companyId, int $unidadeId): bool
    {
        return (bool) $this->createQueryBuilder('userUnidade')
            ->select('COUNT(userUnidade.id)')
            ->andWhere('userUnidade.user = :userId')
            ->andWhere('userUnidade.company = :companyId')
            ->andWhere('userUnidade.unidadeId = :unidadeId')
            ->andWhere('userUnidade.status = :status')
            ->setParameter('userId', $userId)
            ->setParameter('companyId', $companyId)
            ->setParameter('unidadeId', $unidadeId)
            ->setParameter('status', 'active')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function listUnidadeIdsByUser(int $userId, ?int $companyId = null): array
    {
        $qb = $this->createQueryBuilder('userUnidade')
            ->select('userUnidade.unidadeId')
            ->andWhere('userUnidade.user = :userId')
            ->setParameter('userId', $userId);

        if ($companyId !== null) {
            $qb
                ->andWhere('userUnidade.company = :companyId')
                ->setParameter('companyId', $companyId);
        }

        return array_map(
            static fn (array $row): int => (int) $row['unidadeId'],
            $qb->getQuery()->getArrayResult()
        );
    }
}
