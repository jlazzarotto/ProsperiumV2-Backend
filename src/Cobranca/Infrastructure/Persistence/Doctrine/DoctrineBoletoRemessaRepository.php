<?php
declare(strict_types=1);
namespace App\Cobranca\Infrastructure\Persistence\Doctrine;
use App\Cobranca\Domain\Entity\BoletoRemessa;
use App\Cobranca\Domain\Repository\BoletoRemessaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<BoletoRemessa> */
final class DoctrineBoletoRemessaRepository extends ServiceEntityRepository implements BoletoRemessaRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,BoletoRemessa::class);} public function save(BoletoRemessa $remessa): void { $em=$this->getEntityManager(); $em->persist($remessa); $em->flush(); } public function findById(int $id): ?BoletoRemessa { return $this->find($id); } }
