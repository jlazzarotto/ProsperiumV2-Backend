<?php
declare(strict_types=1);
namespace App\Tesouraria\Domain\Repository;
use App\Tesouraria\Domain\Entity\ConciliacaoRegra;
interface ConciliacaoRegraRepositoryInterface { public function save(ConciliacaoRegra $regra): void; /** @return list<ConciliacaoRegra> */ public function listActiveByCompany(int $companyId): array; }
