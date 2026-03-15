<?php
declare(strict_types=1);
namespace App\Cobranca\Infrastructure\Persistence\Doctrine;
use App\Cobranca\Domain\Entity\BorderoRecebimento;
use App\Cobranca\Domain\Repository\BorderoRecebimentoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<BorderoRecebimento> */
final class DoctrineBorderoRecebimentoRepository extends ServiceEntityRepository implements BorderoRecebimentoRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,BorderoRecebimento::class);} public function save(BorderoRecebimento $bordero): void { $em=$this->getEntityManager(); $em->persist($bordero); $em->flush(); } }
