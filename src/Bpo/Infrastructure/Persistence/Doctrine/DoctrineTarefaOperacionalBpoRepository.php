<?php
declare(strict_types=1);
namespace App\Bpo\Infrastructure\Persistence\Doctrine;
use App\Bpo\Domain\Entity\TarefaOperacionalBpo;
use App\Bpo\Domain\Repository\TarefaOperacionalBpoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<TarefaOperacionalBpo> */
final class DoctrineTarefaOperacionalBpoRepository extends ServiceEntityRepository implements TarefaOperacionalBpoRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,TarefaOperacionalBpo::class);}
    public function save(TarefaOperacionalBpo $tarefa): void { $em=$this->getEntityManager(); $em->persist($tarefa); $em->flush(); }
    public function findById(int $id): ?TarefaOperacionalBpo { return $this->find($id); }
    public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $status = null): array { $qb=$this->createQueryBuilder('t')->andWhere('t.company = :companyId')->setParameter('companyId',$companyId)->orderBy('t.id','DESC'); if($empresaId!==null){$qb->andWhere('t.empresa = :empresaId')->setParameter('empresaId',$empresaId);} if($unidadeId!==null){$qb->andWhere('t.unidade = :unidadeId')->setParameter('unidadeId',$unidadeId);} if($status!==null){$qb->andWhere('t.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult(); }
}
