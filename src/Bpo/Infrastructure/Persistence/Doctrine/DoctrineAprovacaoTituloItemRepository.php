<?php
declare(strict_types=1);
namespace App\Bpo\Infrastructure\Persistence\Doctrine;
use App\Bpo\Domain\Entity\AprovacaoTituloItem;
use App\Bpo\Domain\Repository\AprovacaoTituloItemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<AprovacaoTituloItem> */
final class DoctrineAprovacaoTituloItemRepository extends ServiceEntityRepository implements AprovacaoTituloItemRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,AprovacaoTituloItem::class);} public function save(AprovacaoTituloItem $item): void { $em=$this->getEntityManager(); $em->persist($item); $em->flush(); } }
