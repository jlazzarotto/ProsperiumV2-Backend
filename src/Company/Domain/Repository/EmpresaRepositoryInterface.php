<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository;

use App\Company\Domain\Entity\Empresa;

interface EmpresaRepositoryInterface
{
    public function save(Empresa $empresa): void;

    public function findById(int $id): ?Empresa;

    public function existsByCompanyAndCnpj(int $companyId, string $cnpj): bool;

    /**
     * @return list<Empresa>
     */
    public function listAll(?int $companyId = null, ?string $status = null): array;
}
