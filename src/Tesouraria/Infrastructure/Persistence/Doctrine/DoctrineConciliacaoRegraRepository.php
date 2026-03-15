<?php
declare(strict_types=1);
namespace App\Tesouraria\Infrastructure\Persistence\Doctrine;
use App\Tesouraria\Domain\Entity\ConciliacaoRegra;
use App\Tesouraria\Domain\Repository\ConciliacaoRegraRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<ConciliacaoRegra> */
final class DoctrineConciliacaoRegraRepository extends ServiceEntityRepository implements ConciliacaoRegraRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,ConciliacaoRegra::class);} public function save(ConciliacaoRegra $regra): void {$em=$this->getEntityManager();$em->persist($regra);$em->flush();} public function listActiveByCompany(int $companyId): array { return $this->findBy(['company'=>$companyId,'status'=>'active'],['id'=>'ASC']); } }
