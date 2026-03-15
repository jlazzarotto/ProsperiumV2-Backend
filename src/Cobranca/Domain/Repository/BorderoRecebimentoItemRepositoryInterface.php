<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Repository;
use App\Cobranca\Domain\Entity\BorderoRecebimentoItem;
interface BorderoRecebimentoItemRepositoryInterface { public function save(BorderoRecebimentoItem $item): void; }
