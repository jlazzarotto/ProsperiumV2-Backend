<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Repository;
use App\Cobranca\Domain\Entity\PixCobranca;
interface PixCobrancaRepositoryInterface { public function save(PixCobranca $pixCobranca): void; public function findById(int $id): ?PixCobranca; public function findByTxid(string $txid): ?PixCobranca; /** @return list<PixCobranca> */ public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $status = null): array; }
