<?php
declare(strict_types=1);
namespace App\Cadastro\Infrastructure\Persistence\Doctrine;
use App\Cadastro\Domain\Entity\CentroCusto;
use App\Cadastro\Domain\Repository\CentroCustoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<CentroCusto> */
final class DoctrineCentroCustoRepository extends ServiceEntityRepository implements CentroCustoRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,CentroCusto::class);} public function save(CentroCusto $centro): void {$em=$this->getEntityManager();$em->persist($centro);$em->flush();} public function findById(int $id): ?CentroCusto {return $this->find($id);} public function listAll(int $companyId, ?string $status = null): array {$qb=$this->createQueryBuilder('c')->andWhere('c.company = :companyId')->setParameter('companyId',$companyId)->orderBy('c.id','ASC'); if($status!==null){$qb->andWhere('c.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult();} }
