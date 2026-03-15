<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository;

use App\Company\Domain\Entity\TenantInstance;

interface TenantInstanceRepositoryInterface
{
    public function save(TenantInstance $tenantInstance): void;

    public function findByCompanyId(int $companyId): ?TenantInstance;

    public function existsByDatabaseKey(string $databaseKey): bool;

    public function findByDatabaseKey(string $databaseKey): ?TenantInstance;
}
