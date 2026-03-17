<?php
declare(strict_types=1);
namespace App\Contabil\Infrastructure\Persistence\Doctrine;
use App\Contabil\Domain\Entity\LancamentoContabil;
use App\Contabil\Domain\Repository\LancamentoContabilRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<LancamentoContabil> */
final class DoctrineLancamentoContabilRepository extends ServiceEntityRepository implements LancamentoContabilRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,LancamentoContabil::class);}
    public function save(LancamentoContabil $lancamento): void { $em=$this->getEntityManager(); $em->persist($lancamento); $em->flush(); }
    public function findById(int $id): ?LancamentoContabil { return $this->find($id); }
    public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $status = null): array { $qb=$this->createQueryBuilder('l')->andWhere('l.companyId = :companyId')->setParameter('companyId',$companyId)->orderBy('l.id','DESC'); if($empresaId!==null){$qb->andWhere('l.empresa = :empresaId')->setParameter('empresaId',$empresaId);} if($unidadeId!==null){$qb->andWhere('l.unidade = :unidadeId')->setParameter('unidadeId',$unidadeId);} if($status!==null){$qb->andWhere('l.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult(); }
}
