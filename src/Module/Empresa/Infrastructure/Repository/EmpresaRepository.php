<?php

declare(strict_types=1);

namespace App\Module\Empresa\Infrastructure\Repository;

use App\Module\Empresa\Domain\Entity\Empresa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Empresa>
 */
class EmpresaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Empresa::class);
    }

    public function salvar(Empresa $empresa): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($empresa);
        $entityManager->flush();
    }
}
