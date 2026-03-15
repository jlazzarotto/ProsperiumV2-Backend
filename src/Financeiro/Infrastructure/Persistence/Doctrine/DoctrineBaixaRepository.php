<?php
declare(strict_types=1);
namespace App\Financeiro\Infrastructure\Persistence\Doctrine;
use App\Financeiro\Domain\Entity\Baixa;
use App\Financeiro\Domain\Repository\BaixaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<Baixa> */
final class DoctrineBaixaRepository extends ServiceEntityRepository implements BaixaRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,Baixa::class);} public function save(Baixa $baixa): void {$em=$this->getEntityManager();$em->persist($baixa);$em->flush();} public function findById(int $id): ?Baixa { return $this->find($id); } public function findByParcelaId(int $parcelaId): array { return $this->findBy(['parcela'=>$parcelaId],['id'=>'ASC']); } }
