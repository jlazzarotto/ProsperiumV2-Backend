<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Repository;
use App\Cobranca\Domain\Entity\BorderoPagamentoItem;
interface BorderoPagamentoItemRepositoryInterface { public function save(BorderoPagamentoItem $item): void; }
