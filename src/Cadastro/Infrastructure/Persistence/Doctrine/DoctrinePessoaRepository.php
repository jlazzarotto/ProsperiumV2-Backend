<?php
declare(strict_types=1);
namespace App\Cadastro\Infrastructure\Persistence\Doctrine;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Cadastro\Domain\Repository\PessoaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<Pessoa> */
final class DoctrinePessoaRepository extends ServiceEntityRepository implements PessoaRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,Pessoa::class);} public function save(Pessoa $pessoa): void {$em=$this->getEntityManager();$em->persist($pessoa);$em->flush();} public function findById(int $id): ?Pessoa {return $this->find($id);} public function listAll(int $companyId, ?int $empresaId = null, ?string $status = null): array {$qb=$this->createQueryBuilder('p')->andWhere('p.company = :companyId')->setParameter('companyId',$companyId)->orderBy('p.id','ASC'); if($empresaId!==null){$qb->andWhere('p.empresa = :empresaId')->setParameter('empresaId',$empresaId);} if($status!==null){$qb->andWhere('p.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult();} }
