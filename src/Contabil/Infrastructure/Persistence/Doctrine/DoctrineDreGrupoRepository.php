<?php
declare(strict_types=1);
namespace App\Contabil\Infrastructure\Persistence\Doctrine;
use App\Contabil\Domain\Entity\DreGrupo;
use App\Contabil\Domain\Repository\DreGrupoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<DreGrupo> */
final class DoctrineDreGrupoRepository extends ServiceEntityRepository implements DreGrupoRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,DreGrupo::class);}
    public function save(DreGrupo $grupo): void { $em=$this->getEntityManager(); $em->persist($grupo); $em->flush(); }
    public function listAll(int $companyId, ?string $status = null): array { $qb=$this->createQueryBuilder('g')->andWhere('g.company = :companyId')->setParameter('companyId',$companyId)->orderBy('g.ordem','ASC'); if($status!==null){$qb->andWhere('g.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult(); }
}
