<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Persistence\Doctrine;

use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Empresa>
 */
final class DoctrineEmpresaRepository extends ServiceEntityRepository implements EmpresaRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Empresa::class);
    }

    public function save(Empresa $empresa): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($empresa);
        $entityManager->flush();
    }

    public function findById(int $id): ?Empresa
    {
        return $this->find($id);
    }

    public function existsByCompanyAndCnpj(int $companyId, string $cnpj): bool
    {
        return $this->count([
            'company' => $companyId,
            'cnpj' => $cnpj,
        ]) > 0;
    }

    public function listAll(?int $companyId = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('empresa')
            ->orderBy('empresa.id', 'ASC');

        if ($companyId !== null) {
            $qb
                ->andWhere('empresa.company = :companyId')
                ->setParameter('companyId', $companyId);
        }

        if ($status !== null) {
            $qb
                ->andWhere('empresa.status = :status')
                ->setParameter('status', $status);
        }

        /** @var list<Empresa> $items */
        $items = $qb->getQuery()->getResult();

        return $items;
    }
}
