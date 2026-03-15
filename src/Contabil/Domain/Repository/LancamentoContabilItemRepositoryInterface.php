<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Repository;
use App\Contabil\Domain\Entity\LancamentoContabilItem;
interface LancamentoContabilItemRepositoryInterface { public function save(LancamentoContabilItem $item): void; /** @return list<LancamentoContabilItem> */ public function findByLancamentoId(int $lancamentoId): array; }
