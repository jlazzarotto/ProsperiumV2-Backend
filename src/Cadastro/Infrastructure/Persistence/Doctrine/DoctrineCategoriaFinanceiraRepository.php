<?php
declare(strict_types=1);
namespace App\Cadastro\Infrastructure\Persistence\Doctrine;
use App\Cadastro\Domain\Entity\CategoriaFinanceira;
use App\Cadastro\Domain\Repository\CategoriaFinanceiraRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<CategoriaFinanceira> */
final class DoctrineCategoriaFinanceiraRepository extends ServiceEntityRepository implements CategoriaFinanceiraRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,CategoriaFinanceira::class);} public function save(CategoriaFinanceira $categoria): void {$em=$this->getEntityManager();$em->persist($categoria);$em->flush();} public function findById(int $id): ?CategoriaFinanceira {return $this->find($id);} public function listAll(int $companyId, ?string $status = null): array {$qb=$this->createQueryBuilder('c')->andWhere('c.companyId = :companyId')->setParameter('companyId',$companyId)->orderBy('c.id','ASC'); if($status!==null){$qb->andWhere('c.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult();} }
