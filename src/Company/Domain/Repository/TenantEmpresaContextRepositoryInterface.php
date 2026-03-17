<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository;

use App\Company\Domain\Entity\Tenant\Empresa;

interface TenantEmpresaContextRepositoryInterface
{
    public function findById(int $id): ?Empresa;
}
