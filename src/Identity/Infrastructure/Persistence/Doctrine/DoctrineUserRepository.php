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

    public function countAll(): int
    {
        return (int) $this->count([]);
    }

    public function listAll(?int $companyId = null): array
    {
        $qb = $this->createQueryBuilder('user')
            ->orderBy('user.id', 'ASC');

        if ($companyId !== null) {
            $qb
                ->innerJoin('App\Identity\Domain\Entity\UserCompany', 'userCompany', 'ON', 'userCompany.user = user')
                ->andWhere('userCompany.company = :companyId')
                ->setParameter('companyId', $companyId);
        }

        /** @var list<User> $items */
        $items = $qb->getQuery()->getResult();

        return $items;
    }
}
