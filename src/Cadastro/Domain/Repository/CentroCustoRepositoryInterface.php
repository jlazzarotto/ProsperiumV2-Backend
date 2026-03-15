<?php
declare(strict_types=1);
namespace App\Cadastro\Domain\Repository;
use App\Cadastro\Domain\Entity\CentroCusto;
interface CentroCustoRepositoryInterface { public function save(CentroCusto $centro): void; public function findById(int $id): ?CentroCusto; /** @return list<CentroCusto> */ public function listAll(int $companyId, ?string $status = null): array; }
