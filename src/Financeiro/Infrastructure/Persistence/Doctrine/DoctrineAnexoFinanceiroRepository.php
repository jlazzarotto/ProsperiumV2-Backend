<?php
declare(strict_types=1);
namespace App\Financeiro\Infrastructure\Persistence\Doctrine;
use App\Financeiro\Domain\Entity\AnexoFinanceiro;
use App\Financeiro\Domain\Repository\AnexoFinanceiroRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<AnexoFinanceiro> */
final class DoctrineAnexoFinanceiroRepository extends ServiceEntityRepository implements AnexoFinanceiroRepositoryInterface { public function __construct(ManagerRegistry $r){parent::__construct($r,AnexoFinanceiro::class);} public function save(AnexoFinanceiro $anexo): void {$em=$this->getEntityManager();$em->persist($anexo);$em->flush();} public function findByTituloId(int $tituloId): array { return $this->findBy(['titulo'=>$tituloId],['id'=>'ASC']); } }
