<?php
declare(strict_types=1);
namespace App\Cobranca\Infrastructure\Persistence\Doctrine;
use App\Cobranca\Domain\Entity\PixRecebimento;
use App\Cobranca\Domain\Repository\PixRecebimentoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<PixRecebimento> */
final class DoctrinePixRecebimentoRepository extends ServiceEntityRepository implements PixRecebimentoRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,PixRecebimento::class);} public function save(PixRecebimento $recebimento): void { $em=$this->getEntityManager(); $em->persist($recebimento); $em->flush(); } public function findByEndToEndId(string $endToEndId): ?PixRecebimento { return $this->findOneBy(['endToEndId' => $endToEndId]); } }
