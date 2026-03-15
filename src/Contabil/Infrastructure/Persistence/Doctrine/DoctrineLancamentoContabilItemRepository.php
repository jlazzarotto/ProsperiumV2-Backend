<?php
declare(strict_types=1);
namespace App\Contabil\Infrastructure\Persistence\Doctrine;
use App\Contabil\Domain\Entity\LancamentoContabilItem;
use App\Contabil\Domain\Repository\LancamentoContabilItemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<LancamentoContabilItem> */
final class DoctrineLancamentoContabilItemRepository extends ServiceEntityRepository implements LancamentoContabilItemRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,LancamentoContabilItem::class);}
    public function save(LancamentoContabilItem $item): void { $em=$this->getEntityManager(); $em->persist($item); $em->flush(); }
    public function findByLancamentoId(int $lancamentoId): array { return $this->findBy(['lancamento' => $lancamentoId], ['id' => 'ASC']); }
}
