<?php
declare(strict_types=1);
namespace App\Bpo\Infrastructure\Persistence\Doctrine;
use App\Bpo\Domain\Entity\ComentarioTitulo;
use App\Bpo\Domain\Repository\ComentarioTituloRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<ComentarioTitulo> */
final class DoctrineComentarioTituloRepository extends ServiceEntityRepository implements ComentarioTituloRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,ComentarioTitulo::class);} public function save(ComentarioTitulo $comentario): void { $em=$this->getEntityManager(); $em->persist($comentario); $em->flush(); } }
