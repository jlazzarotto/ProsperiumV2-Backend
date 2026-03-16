<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
final class DoctrineUserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    public function findById(int $id): ?User
    {
        return $this->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => mb_strtolower(trim($email))]);
    }

    public function findByEmailAndCompany(string $email, int $companyId): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->andWhere('u.company = :companyId')
            ->setParameter('email', mb_strtolower(trim($email)))
            ->setParameter('companyId', $companyId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countAll(): int
    {
        return (int) $this->count([]);
    }

    public function listAll(?int $companyId = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('user')
            ->orderBy('user.id', 'ASC');

        if ($companyId !== null) {
            $qb
                ->andWhere('user.company = :companyId')
                ->setParameter('companyId', $companyId);
        }

        if ($status !== null) {
            $qb
                ->andWhere('user.status = :status')
                ->setParameter('status', $status);
        }

        /** @var list<User> $items */
        $items = $qb->getQuery()->getResult();

        return $items;
    }
}
