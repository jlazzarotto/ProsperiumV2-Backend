<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository;

use App\Company\Domain\Entity\Empresa;

interface TenantEmpresaContextRepositoryInterface
{
    public function findById(int $id): ?Empresa;
}
