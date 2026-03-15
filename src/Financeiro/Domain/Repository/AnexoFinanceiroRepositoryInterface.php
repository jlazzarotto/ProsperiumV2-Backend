<?php
declare(strict_types=1);
namespace App\Financeiro\Domain\Repository;
use App\Financeiro\Domain\Entity\AnexoFinanceiro;
interface AnexoFinanceiroRepositoryInterface { public function save(AnexoFinanceiro $anexo): void; /** @return list<AnexoFinanceiro> */ public function findByTituloId(int $tituloId): array; }
