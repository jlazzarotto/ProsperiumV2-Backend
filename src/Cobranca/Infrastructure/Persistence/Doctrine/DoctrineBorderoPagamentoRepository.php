<?php
declare(strict_types=1);
namespace App\Cobranca\Infrastructure\Persistence\Doctrine;
use App\Cobranca\Domain\Entity\BorderoPagamento;
use App\Cobranca\Domain\Repository\BorderoPagamentoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<BorderoPagamento> */
final class DoctrineBorderoPagamentoRepository extends ServiceEntityRepository implements BorderoPagamentoRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,BorderoPagamento::class);} public function save(BorderoPagamento $bordero): void { $em=$this->getEntityManager(); $em->persist($bordero); $em->flush(); } }
