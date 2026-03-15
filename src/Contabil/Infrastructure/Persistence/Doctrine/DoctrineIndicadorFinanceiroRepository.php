<?php
declare(strict_types=1);
namespace App\Contabil\Infrastructure\Persistence\Doctrine;
use App\Contabil\Domain\Entity\IndicadorFinanceiro;
use App\Contabil\Domain\Repository\IndicadorFinanceiroRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<IndicadorFinanceiro> */
final class DoctrineIndicadorFinanceiroRepository extends ServiceEntityRepository implements IndicadorFinanceiroRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,IndicadorFinanceiro::class);}
    public function save(IndicadorFinanceiro $indicador): void { $em=$this->getEntityManager(); $em->persist($indicador); $em->flush(); }
    public function listByDate(int $companyId, int $empresaId, int $unidadeId, \DateTimeImmutable $dataReferencia): array { return $this->findBy(['company'=>$companyId,'empresa'=>$empresaId,'unidade'=>$unidadeId,'dataReferencia'=>$dataReferencia], ['codigo' => 'ASC', 'id' => 'DESC']); }
}
