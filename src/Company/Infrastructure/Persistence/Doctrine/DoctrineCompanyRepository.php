<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence\Doctrine;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 */
final class DoctrineCompanyRepository extends ServiceEntityRepository implements CompanyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function save(Company $company): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($company);
        $entityManager->flush();
    }

    public function findById(int $id): ?Company
    {
        return $this->find($id);
    }

    public function countAll(): int
    {
        return (int) $this->count([]);
    }

    public function listAll(?string $status = null): array
    {
        $qb = $this->createQueryBuilder('company')
            ->orderBy('company.id', 'ASC');

        if ($status !== null) {
            $qb
                ->andWhere('company.status = :status')
                ->setParameter('status', $status);
        }

        /** @var list<Company> $items */
        $items = $qb->getQuery()->getResult();

        return $items;
    }
}
