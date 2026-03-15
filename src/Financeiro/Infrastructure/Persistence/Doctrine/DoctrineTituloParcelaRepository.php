<?php
declare(strict_types=1);
namespace App\Financeiro\Infrastructure\Persistence\Doctrine;
use App\Financeiro\Domain\Entity\TituloParcela;
use App\Financeiro\Domain\Repository\TituloParcelaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<TituloParcela> */
final class DoctrineTituloParcelaRepository extends ServiceEntityRepository implements TituloParcelaRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,TituloParcela::class);} public function save(TituloParcela $parcela): void {$em=$this->getEntityManager();$em->persist($parcela);$em->flush();} public function removeByTituloId(int $tituloId): void { $this->getEntityManager()->createQuery('DELETE FROM App\\Financeiro\\Domain\\Entity\\TituloParcela p WHERE p.titulo = :tituloId')->setParameter('tituloId',$tituloId)->execute(); } public function findById(int $id): ?TituloParcela { return $this->find($id);} public function findByTituloId(int $tituloId): array { return $this->findBy(['titulo'=>$tituloId],['numero'=>'ASC']); } }
