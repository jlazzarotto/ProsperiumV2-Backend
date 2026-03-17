<?php
declare(strict_types=1);
namespace App\Cobranca\Infrastructure\Persistence\Doctrine;
use App\Cobranca\Domain\Entity\PixCobranca;
use App\Cobranca\Domain\Repository\PixCobrancaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<PixCobranca> */
final class DoctrinePixCobrancaRepository extends ServiceEntityRepository implements PixCobrancaRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,PixCobranca::class);}
    public function save(PixCobranca $pixCobranca): void { $em=$this->getEntityManager(); $em->persist($pixCobranca); $em->flush(); }
    public function findById(int $id): ?PixCobranca { return $this->find($id); }
    public function findByTxid(string $txid): ?PixCobranca { return $this->findOneBy(['txid' => $txid]); }
    public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $status = null): array { $qb=$this->createQueryBuilder('p')->andWhere('p.companyId = :companyId')->setParameter('companyId',$companyId)->orderBy('p.id','DESC'); if($empresaId!==null){$qb->andWhere('p.empresa = :empresaId')->setParameter('empresaId',$empresaId);} if($unidadeId!==null){$qb->andWhere('p.unidade = :unidadeId')->setParameter('unidadeId',$unidadeId);} if($status!==null){$qb->andWhere('p.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult(); }
}
