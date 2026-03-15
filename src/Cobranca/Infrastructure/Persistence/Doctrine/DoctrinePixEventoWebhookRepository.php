<?php
declare(strict_types=1);
namespace App\Cobranca\Infrastructure\Persistence\Doctrine;
use App\Cobranca\Domain\Entity\PixEventoWebhook;
use App\Cobranca\Domain\Repository\PixEventoWebhookRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<PixEventoWebhook> */
final class DoctrinePixEventoWebhookRepository extends ServiceEntityRepository implements PixEventoWebhookRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,PixEventoWebhook::class);} public function save(PixEventoWebhook $evento): void { $em=$this->getEntityManager(); $em->persist($evento); $em->flush(); } }
