<?php
declare(strict_types=1);
namespace App\Cobranca\Infrastructure\Persistence\Doctrine;
use App\Cobranca\Domain\Entity\BoletoRemessaItem;
use App\Cobranca\Domain\Repository\BoletoRemessaItemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<BoletoRemessaItem> */
final class DoctrineBoletoRemessaItemRepository extends ServiceEntityRepository implements BoletoRemessaItemRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,BoletoRemessaItem::class);}
    public function save(BoletoRemessaItem $item): void { $em=$this->getEntityManager(); $em->persist($item); $em->flush(); }
    public function findByNossoNumero(string $nossoNumero): ?BoletoRemessaItem { return $this->findOneBy(['nossoNumero' => $nossoNumero]); }
    public function findByRemessaId(int $remessaId): array { return $this->findBy(['remessa' => $remessaId], ['id' => 'ASC']); }
}
