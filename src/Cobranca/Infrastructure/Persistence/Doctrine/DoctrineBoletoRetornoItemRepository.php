<?php
declare(strict_types=1);
namespace App\Cobranca\Infrastructure\Persistence\Doctrine;
use App\Cobranca\Domain\Entity\BoletoRetornoItem;
use App\Cobranca\Domain\Repository\BoletoRetornoItemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<BoletoRetornoItem> */
final class DoctrineBoletoRetornoItemRepository extends ServiceEntityRepository implements BoletoRetornoItemRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,BoletoRetornoItem::class);} public function save(BoletoRetornoItem $item): void { $em=$this->getEntityManager(); $em->persist($item); $em->flush(); } }
