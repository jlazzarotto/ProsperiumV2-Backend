<?php
declare(strict_types=1);
namespace App\Tesouraria\Infrastructure\Persistence\Doctrine;
use App\Tesouraria\Domain\Entity\IntegracaoBancaria;
use App\Tesouraria\Domain\Repository\IntegracaoBancariaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<IntegracaoBancaria> */
final class DoctrineIntegracaoBancariaRepository extends ServiceEntityRepository implements IntegracaoBancariaRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,IntegracaoBancaria::class);} public function save(IntegracaoBancaria $integracao): void {$em=$this->getEntityManager();$em->persist($integracao);$em->flush();} }
