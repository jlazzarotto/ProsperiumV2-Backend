<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Repository;
use App\Cobranca\Domain\Entity\BoletoRemessaItem;
interface BoletoRemessaItemRepositoryInterface { public function save(BoletoRemessaItem $item): void; public function findByNossoNumero(string $nossoNumero): ?BoletoRemessaItem; /** @return list<BoletoRemessaItem> */ public function findByRemessaId(int $remessaId): array; }
