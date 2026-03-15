<?php
declare(strict_types=1);
namespace App\Cobranca\Infrastructure\Persistence\Doctrine;
use App\Cobranca\Domain\Entity\BorderoPagamentoItem;
use App\Cobranca\Domain\Repository\BorderoPagamentoItemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<BorderoPagamentoItem> */
final class DoctrineBorderoPagamentoItemRepository extends ServiceEntityRepository implements BorderoPagamentoItemRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,BorderoPagamentoItem::class);} public function save(BorderoPagamentoItem $item): void { $em=$this->getEntityManager(); $em->persist($item); $em->flush(); } }
