<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Repository;
use App\Contabil\Domain\Entity\LancamentoContabil;
interface LancamentoContabilRepositoryInterface { public function save(LancamentoContabil $lancamento): void; public function findById(int $id): ?LancamentoContabil; /** @return list<LancamentoContabil> */ public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $status = null): array; }
