<?php
declare(strict_types=1);
namespace App\Financeiro\Infrastructure\Persistence\Doctrine;
use App\Financeiro\Domain\Entity\MovimentoFinanceiro;
use App\Financeiro\Domain\Repository\MovimentoFinanceiroRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<MovimentoFinanceiro> */
final class DoctrineMovimentoFinanceiroRepository extends ServiceEntityRepository implements MovimentoFinanceiroRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,MovimentoFinanceiro::class);} public function save(MovimentoFinanceiro $movimento): void {$em=$this->getEntityManager();$em->persist($movimento);$em->flush();} public function findById(int $id): ?MovimentoFinanceiro { return $this->find($id); } public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $tipo = null, ?int $contaFinanceiraId = null): array { $qb=$this->createQueryBuilder('m')->andWhere('m.companyId = :companyId')->setParameter('companyId',$companyId)->orderBy('m.id','DESC'); if($empresaId!==null){$qb->andWhere('m.empresa = :empresaId')->setParameter('empresaId',$empresaId);} if($unidadeId!==null){$qb->andWhere('m.unidade = :unidadeId')->setParameter('unidadeId',$unidadeId);} if($tipo!==null){$qb->andWhere('m.tipo = :tipo')->setParameter('tipo',$tipo);} if($contaFinanceiraId!==null){$qb->andWhere('m.contaFinanceira = :contaFinanceiraId')->setParameter('contaFinanceiraId',$contaFinanceiraId);} return $qb->getQuery()->getResult(); } }
