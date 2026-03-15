<?php
declare(strict_types=1);
namespace App\Bpo\Infrastructure\Persistence\Doctrine;
use App\Bpo\Domain\Entity\AprovacaoTitulo;
use App\Bpo\Domain\Repository\AprovacaoTituloRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<AprovacaoTitulo> */
final class DoctrineAprovacaoTituloRepository extends ServiceEntityRepository implements AprovacaoTituloRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,AprovacaoTitulo::class);} public function save(AprovacaoTitulo $aprovacao): void { $em=$this->getEntityManager(); $em->persist($aprovacao); $em->flush(); } public function findById(int $id): ?AprovacaoTitulo { return $this->find($id); } }
