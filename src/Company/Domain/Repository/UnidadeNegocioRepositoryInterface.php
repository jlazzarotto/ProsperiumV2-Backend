<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository;

use App\Company\Domain\Entity\Tenant\UnidadeNegocio;

interface UnidadeNegocioRepositoryInterface
{
    public function save(UnidadeNegocio $unidadeNegocio): void;

    public function findById(int $id): ?UnidadeNegocio;

    public function existsByCompanyAndNome(int $companyId, string $nome): bool;

    public function existsByCompanyAndAbreviatura(int $companyId, string $abreviatura): bool;

    /**
     * @return list<UnidadeNegocio>
     */
    public function listAll(?int $companyId = null, ?string $status = null): array;
}
