<?php
declare(strict_types=1);
namespace App\Cadastro\Domain\Repository;
use App\Cadastro\Domain\Entity\CategoriaFinanceira;
interface CategoriaFinanceiraRepositoryInterface { public function save(CategoriaFinanceira $categoria): void; public function findById(int $id): ?CategoriaFinanceira; /** @return list<CategoriaFinanceira> */ public function listAll(int $companyId, ?string $status = null): array; }
