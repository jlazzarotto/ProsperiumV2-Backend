<?php
declare(strict_types=1);
namespace App\Cadastro\Infrastructure\Persistence\Doctrine;
use App\Cadastro\Domain\Entity\FormaPagamento;
use App\Cadastro\Domain\Repository\FormaPagamentoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<FormaPagamento> */
final class DoctrineFormaPagamentoRepository extends ServiceEntityRepository implements FormaPagamentoRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,FormaPagamento::class);} public function save(FormaPagamento $formaPagamento): void {$em=$this->getEntityManager();$em->persist($formaPagamento);$em->flush();} public function findById(int $id): ?FormaPagamento {return $this->find($id);} public function listAll(int $companyId, ?string $status = null): array {$qb=$this->createQueryBuilder('f')->andWhere('f.company = :companyId')->setParameter('companyId',$companyId)->orderBy('f.id','ASC'); if($status!==null){$qb->andWhere('f.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult();} }
