<?php
declare(strict_types=1);
namespace App\Contabil\Infrastructure\Persistence\Doctrine;
use App\Contabil\Domain\Entity\ContaContabil;
use App\Contabil\Domain\Repository\ContaContabilRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<ContaContabil> */
final class DoctrineContaContabilRepository extends ServiceEntityRepository implements ContaContabilRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,ContaContabil::class);}
    public function save(ContaContabil $conta): void { $em=$this->getEntityManager(); $em->persist($conta); $em->flush(); }
    public function findById(int $id): ?ContaContabil { return $this->find($id); }
    public function listAll(int $companyId, ?string $tipo = null, ?string $status = null): array { $qb=$this->createQueryBuilder('c')->andWhere('c.companyId = :companyId')->setParameter('companyId',$companyId)->orderBy('c.codigo','ASC'); if($tipo!==null){$qb->andWhere('c.tipo = :tipo')->setParameter('tipo',$tipo);} if($status!==null){$qb->andWhere('c.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult(); }
}
