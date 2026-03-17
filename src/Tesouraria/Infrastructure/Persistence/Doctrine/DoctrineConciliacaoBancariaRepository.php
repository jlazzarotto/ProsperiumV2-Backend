<?php
declare(strict_types=1);
namespace App\Tesouraria\Infrastructure\Persistence\Doctrine;
use App\Tesouraria\Domain\Entity\ConciliacaoBancaria;
use App\Tesouraria\Domain\Repository\ConciliacaoBancariaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<ConciliacaoBancaria> */
final class DoctrineConciliacaoBancariaRepository extends ServiceEntityRepository implements ConciliacaoBancariaRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,ConciliacaoBancaria::class);} public function save(ConciliacaoBancaria $conciliacao): void {$em=$this->getEntityManager();$em->persist($conciliacao);$em->flush();} public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null): array {$qb=$this->createQueryBuilder('c')->innerJoin('c.extratoBancario','e')->andWhere('e.companyId = :companyId')->setParameter('companyId',$companyId)->orderBy('c.id','DESC'); if($empresaId!==null){$qb->andWhere('e.empresa = :empresaId')->setParameter('empresaId',$empresaId);} if($unidadeId!==null){$qb->andWhere('e.unidade = :unidadeId')->setParameter('unidadeId',$unidadeId);} return $qb->getQuery()->getResult();} }
