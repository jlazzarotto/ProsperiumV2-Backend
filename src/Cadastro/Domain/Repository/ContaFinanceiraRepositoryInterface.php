<?php
declare(strict_types=1);
namespace App\Cadastro\Domain\Repository;
use App\Cadastro\Domain\Entity\ContaFinanceira;
interface ContaFinanceiraRepositoryInterface { public function save(ContaFinanceira $conta): void; public function findById(int $id): ?ContaFinanceira; /** @return list<ContaFinanceira> */ public function listAll(int $companyId, ?int $empresaId = null, ?string $status = null): array; }
