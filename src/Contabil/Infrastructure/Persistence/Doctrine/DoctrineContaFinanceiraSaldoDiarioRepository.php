<?php
declare(strict_types=1);
namespace App\Contabil\Infrastructure\Persistence\Doctrine;
use App\Contabil\Domain\Entity\ContaFinanceiraSaldoDiario;
use App\Contabil\Domain\Repository\ContaFinanceiraSaldoDiarioRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<ContaFinanceiraSaldoDiario> */
final class DoctrineContaFinanceiraSaldoDiarioRepository extends ServiceEntityRepository implements ContaFinanceiraSaldoDiarioRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,ContaFinanceiraSaldoDiario::class);}
    public function save(ContaFinanceiraSaldoDiario $saldo): void { $em=$this->getEntityManager(); $em->persist($saldo); $em->flush(); }
    public function findByContextContaAndDate(int $companyId, int $empresaId, int $unidadeId, int $contaFinanceiraId, \DateTimeImmutable $data): ?ContaFinanceiraSaldoDiario { return $this->findOneBy(['company'=>$companyId,'empresa'=>$empresaId,'unidade'=>$unidadeId,'contaFinanceira'=>$contaFinanceiraId,'dataSaldo'=>$data]); }
    public function listByPeriodo(int $companyId, int $empresaId, int $unidadeId, \DateTimeImmutable $inicio, \DateTimeImmutable $fim): array { return $this->createQueryBuilder('s')->andWhere('s.companyId = :companyId')->andWhere('s.empresa = :empresaId')->andWhere('s.unidade = :unidadeId')->andWhere('s.dataSaldo BETWEEN :inicio AND :fim')->setParameter('companyId',$companyId)->setParameter('empresaId',$empresaId)->setParameter('unidadeId',$unidadeId)->setParameter('inicio',$inicio)->setParameter('fim',$fim)->orderBy('s.dataSaldo','ASC')->getQuery()->getResult(); }
}
