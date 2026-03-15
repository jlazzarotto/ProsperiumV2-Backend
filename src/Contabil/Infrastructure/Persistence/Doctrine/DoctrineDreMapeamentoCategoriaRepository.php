<?php
declare(strict_types=1);
namespace App\Contabil\Infrastructure\Persistence\Doctrine;
use App\Contabil\Domain\Entity\DreMapeamentoCategoria;
use App\Contabil\Domain\Repository\DreMapeamentoCategoriaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<DreMapeamentoCategoria> */
final class DoctrineDreMapeamentoCategoriaRepository extends ServiceEntityRepository implements DreMapeamentoCategoriaRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,DreMapeamentoCategoria::class);}
    public function save(DreMapeamentoCategoria $mapeamento): void { $em=$this->getEntityManager(); $em->persist($mapeamento); $em->flush(); }
    public function findByCompanyId(int $companyId): array { return $this->findBy(['company' => $companyId], ['id' => 'ASC']); }
}
