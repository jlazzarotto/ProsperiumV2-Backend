<?php
declare(strict_types=1);
namespace App\Financeiro\Infrastructure\Persistence\Doctrine;
use App\Financeiro\Domain\Entity\Titulo;
use App\Financeiro\Domain\Repository\TituloRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<Titulo> */
final class DoctrineTituloRepository extends ServiceEntityRepository implements TituloRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,Titulo::class);} public function save(Titulo $titulo): void {$em=$this->getEntityManager();$em->persist($titulo);$em->flush();} public function findById(int $id): ?Titulo {return $this->find($id);} public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $tipo = null, ?string $status = null): array {$qb=$this->createQueryBuilder('t')->andWhere('t.company = :companyId')->setParameter('companyId',$companyId)->orderBy('t.id','DESC'); if($empresaId!==null){$qb->andWhere('t.empresa = :empresaId')->setParameter('empresaId',$empresaId);} if($unidadeId!==null){$qb->andWhere('t.unidade = :unidadeId')->setParameter('unidadeId',$unidadeId);} if($tipo!==null){$qb->andWhere('t.tipo = :tipo')->setParameter('tipo',$tipo);} if($status!==null){$qb->andWhere('t.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult();} }
