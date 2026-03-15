<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Repository;
use App\Contabil\Domain\Entity\ContaFinanceiraSaldoDiario;
interface ContaFinanceiraSaldoDiarioRepositoryInterface { public function save(ContaFinanceiraSaldoDiario $saldo): void; public function findByContextContaAndDate(int $companyId, int $empresaId, int $unidadeId, int $contaFinanceiraId, \DateTimeImmutable $data): ?ContaFinanceiraSaldoDiario; /** @return list<ContaFinanceiraSaldoDiario> */ public function listByPeriodo(int $companyId, int $empresaId, int $unidadeId, \DateTimeImmutable $inicio, \DateTimeImmutable $fim): array; }
