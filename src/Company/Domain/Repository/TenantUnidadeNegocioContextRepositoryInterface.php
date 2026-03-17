<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository;

use App\Company\Domain\Entity\Tenant\UnidadeNegocio;

interface TenantUnidadeNegocioContextRepositoryInterface
{
    public function findById(int $id): ?UnidadeNegocio;
}
