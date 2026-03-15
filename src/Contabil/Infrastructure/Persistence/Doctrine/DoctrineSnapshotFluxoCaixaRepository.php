<?php
declare(strict_types=1);
namespace App\Contabil\Infrastructure\Persistence\Doctrine;
use App\Contabil\Domain\Entity\SnapshotFluxoCaixa;
use App\Contabil\Domain\Repository\SnapshotFluxoCaixaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<SnapshotFluxoCaixa> */
final class DoctrineSnapshotFluxoCaixaRepository extends ServiceEntityRepository implements SnapshotFluxoCaixaRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,SnapshotFluxoCaixa::class);}
    public function save(SnapshotFluxoCaixa $snapshot): void { $em=$this->getEntityManager(); $em->persist($snapshot); $em->flush(); }
    public function findByContextAndDate(int $companyId, int $empresaId, int $unidadeId, \DateTimeImmutable $data): ?SnapshotFluxoCaixa { return $this->findOneBy(['company'=>$companyId,'empresa'=>$empresaId,'unidade'=>$unidadeId,'dataReferencia'=>$data]); }
}
