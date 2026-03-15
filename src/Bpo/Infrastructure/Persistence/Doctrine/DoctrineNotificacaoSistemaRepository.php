<?php
declare(strict_types=1);
namespace App\Bpo\Infrastructure\Persistence\Doctrine;
use App\Bpo\Domain\Entity\NotificacaoSistema;
use App\Bpo\Domain\Repository\NotificacaoSistemaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<NotificacaoSistema> */
final class DoctrineNotificacaoSistemaRepository extends ServiceEntityRepository implements NotificacaoSistemaRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,NotificacaoSistema::class);} public function save(NotificacaoSistema $notificacao): void { $em=$this->getEntityManager(); $em->persist($notificacao); $em->flush(); } }
