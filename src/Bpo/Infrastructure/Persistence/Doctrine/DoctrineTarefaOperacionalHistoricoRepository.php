<?php
declare(strict_types=1);
namespace App\Bpo\Infrastructure\Persistence\Doctrine;
use App\Bpo\Domain\Entity\TarefaOperacionalHistorico;
use App\Bpo\Domain\Repository\TarefaOperacionalHistoricoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<TarefaOperacionalHistorico> */
final class DoctrineTarefaOperacionalHistoricoRepository extends ServiceEntityRepository implements TarefaOperacionalHistoricoRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,TarefaOperacionalHistorico::class);} public function save(TarefaOperacionalHistorico $historico): void { $em=$this->getEntityManager(); $em->persist($historico); $em->flush(); } }
