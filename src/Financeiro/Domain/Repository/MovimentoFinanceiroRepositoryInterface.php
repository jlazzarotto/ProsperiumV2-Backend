<?php
declare(strict_types=1);
namespace App\Financeiro\Domain\Repository;
use App\Financeiro\Domain\Entity\MovimentoFinanceiro;
interface MovimentoFinanceiroRepositoryInterface { public function save(MovimentoFinanceiro $movimento): void; public function findById(int $id): ?MovimentoFinanceiro; /** @return list<MovimentoFinanceiro> */ public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $tipo = null, ?int $contaFinanceiraId = null): array; }
