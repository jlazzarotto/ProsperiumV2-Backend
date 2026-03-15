<?php
declare(strict_types=1);
namespace App\Cobranca\Infrastructure\Persistence\Doctrine;
use App\Cobranca\Domain\Entity\BorderoRecebimentoItem;
use App\Cobranca\Domain\Repository\BorderoRecebimentoItemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<BorderoRecebimentoItem> */
final class DoctrineBorderoRecebimentoItemRepository extends ServiceEntityRepository implements BorderoRecebimentoItemRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,BorderoRecebimentoItem::class);} public function save(BorderoRecebimentoItem $item): void { $em=$this->getEntityManager(); $em->persist($item); $em->flush(); } }
