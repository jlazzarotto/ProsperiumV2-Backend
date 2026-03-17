<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\UserEmpresa;
use App\Identity\Domain\Repository\UserEmpresaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserEmpresa>
 */
final class DoctrineUserEmpresaRepository extends ServiceEntityRepository implements UserEmpresaRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEmpresa::class);
    }

    public function save(UserEmpresa $userEmpresa): void
    {
        $em = $this->getEntityManager();
        $em->persist($userEmpresa);
        $em->flush();
    }

    public function userHasEmpresa(int $userId, int $companyId, int $empresaId): bool
    {
        return (bool) $this->createQueryBuilder('userEmpresa')
            ->select('COUNT(userEmpresa.id)')
            ->andWhere('userEmpresa.user = :userId')
            ->andWhere('userEmpresa.company = :companyId')
            ->andWhere('userEmpresa.empresaId = :empresaId')
            ->andWhere('userEmpresa.status = :status')
            ->setParameter('userId', $userId)
            ->setParameter('companyId', $companyId)
            ->setParameter('empresaId', $empresaId)
            ->setParameter('status', 'active')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function listEmpresaIdsByUser(int $userId, ?int $companyId = null): array
    {
        $qb = $this->createQueryBuilder('userEmpresa')
            ->select('userEmpresa.empresaId')
            ->andWhere('userEmpresa.user = :userId')
            ->setParameter('userId', $userId);

        if ($companyId !== null) {
            $qb
                ->andWhere('userEmpresa.company = :companyId')
                ->setParameter('companyId', $companyId);
        }

        return array_map(
            static fn (array $row): int => (int) $row['empresaId'],
            $qb->getQuery()->getArrayResult()
        );
    }
}
