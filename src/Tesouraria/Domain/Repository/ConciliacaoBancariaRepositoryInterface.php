<?php
declare(strict_types=1);
namespace App\Tesouraria\Domain\Repository;
use App\Tesouraria\Domain\Entity\ConciliacaoBancaria;
interface ConciliacaoBancariaRepositoryInterface { public function save(ConciliacaoBancaria $conciliacao): void; /** @return list<ConciliacaoBancaria> */ public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null): array; }
