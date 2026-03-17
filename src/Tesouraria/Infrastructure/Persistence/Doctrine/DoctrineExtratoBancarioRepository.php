<?php
declare(strict_types=1);
namespace App\Tesouraria\Infrastructure\Persistence\Doctrine;
use App\Tesouraria\Domain\Entity\ExtratoBancario;
use App\Tesouraria\Domain\Repository\ExtratoBancarioRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<ExtratoBancario> */
final class DoctrineExtratoBancarioRepository extends ServiceEntityRepository implements ExtratoBancarioRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,ExtratoBancario::class);} public function save(ExtratoBancario $extrato): void {$em=$this->getEntityManager();$em->persist($extrato);$em->flush();} public function findById(int $id): ?ExtratoBancario {return $this->find($id);} public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?int $contaFinanceiraId = null, ?string $status = null): array {$qb=$this->createQueryBuilder('e')->andWhere('e.companyId = :companyId')->setParameter('companyId',$companyId)->orderBy('e.dataMovimento','DESC'); if($empresaId!==null){$qb->andWhere('e.empresa = :empresaId')->setParameter('empresaId',$empresaId);} if($unidadeId!==null){$qb->andWhere('e.unidade = :unidadeId')->setParameter('unidadeId',$unidadeId);} if($contaFinanceiraId!==null){$qb->andWhere('e.contaFinanceira = :contaFinanceiraId')->setParameter('contaFinanceiraId',$contaFinanceiraId);} if($status!==null){$qb->andWhere('e.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult();} }
