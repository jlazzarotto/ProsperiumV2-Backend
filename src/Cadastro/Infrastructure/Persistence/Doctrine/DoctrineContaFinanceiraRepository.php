<?php
declare(strict_types=1);
namespace App\Cadastro\Infrastructure\Persistence\Doctrine;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Cadastro\Domain\Repository\ContaFinanceiraRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<ContaFinanceira> */
final class DoctrineContaFinanceiraRepository extends ServiceEntityRepository implements ContaFinanceiraRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,ContaFinanceira::class);} public function save(ContaFinanceira $conta): void {$em=$this->getEntityManager();$em->persist($conta);$em->flush();} public function findById(int $id): ?ContaFinanceira {return $this->find($id);} public function listAll(int $companyId, ?int $empresaId = null, ?string $status = null): array {$qb=$this->createQueryBuilder('c')->andWhere('c.companyId = :companyId')->setParameter('companyId',$companyId)->orderBy('c.id','ASC'); if($empresaId!==null){$qb->andWhere('c.empresa = :empresaId')->setParameter('empresaId',$empresaId);} if($status!==null){$qb->andWhere('c.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult();} }
