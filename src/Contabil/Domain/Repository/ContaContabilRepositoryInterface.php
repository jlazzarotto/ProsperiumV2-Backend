<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Repository;
use App\Contabil\Domain\Entity\ContaContabil;
interface ContaContabilRepositoryInterface { public function save(ContaContabil $conta): void; public function findById(int $id): ?ContaContabil; /** @return list<ContaContabil> */ public function listAll(int $companyId, ?string $tipo = null, ?string $status = null): array; }
