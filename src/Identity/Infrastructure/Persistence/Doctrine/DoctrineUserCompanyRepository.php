<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\UserCompany;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCompany>
 */
final class DoctrineUserCompanyRepository extends ServiceEntityRepository implements UserCompanyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCompany::class);
    }

    public function save(UserCompany $userCompany): void
    {
        $em = $this->getEntityManager();
        $em->persist($userCompany);
        $em->flush();
    }

    public function userHasCompany(int $userId, int $companyId): bool
    {
        return $this->createQueryBuilder('uc')
            ->select('COUNT(uc.id)')
            ->andWhere('uc.user = :userId')
            ->andWhere('uc.company = :companyId')
            ->setParameter('userId', $userId)
            ->setParameter('companyId', $companyId)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function isCompanyAdmin(int $userId, int $companyId): bool
    {
        return $this->createQueryBuilder('uc')
            ->select('COUNT(uc.id)')
            ->andWhere('uc.user = :userId')
            ->andWhere('uc.company = :companyId')
            ->andWhere('uc.isCompanyAdmin = true')
            ->setParameter('userId', $userId)
            ->setParameter('companyId', $companyId)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function listCompanyIdsByUser(int $userId): array
    {
        $rows = $this->createQueryBuilder('userCompany')
            ->select('IDENTITY(userCompany.company) AS companyId')
            ->andWhere('userCompany.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getArrayResult();

        return array_map(static fn (array $row): int => (int) $row['companyId'], $rows);
    }
}
