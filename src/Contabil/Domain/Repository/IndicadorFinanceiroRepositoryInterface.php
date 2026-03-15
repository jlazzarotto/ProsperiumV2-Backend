<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Repository;
use App\Contabil\Domain\Entity\IndicadorFinanceiro;
interface IndicadorFinanceiroRepositoryInterface { public function save(IndicadorFinanceiro $indicador): void; /** @return list<IndicadorFinanceiro> */ public function listByDate(int $companyId, int $empresaId, int $unidadeId, \DateTimeImmutable $dataReferencia): array; }
