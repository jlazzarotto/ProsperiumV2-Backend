<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MultiTenancy;

final class TenantDatabaseConfigRegistry
{
    /**
     * @param array<string, mixed>|null $tenants
     */
    public function __construct(private readonly ?array $tenants = null)
    {
    }

    public function findDatabaseUrl(string $databaseKey): ?string
    {
        $tenantConfig = $this->tenants[$databaseKey] ?? null;

        if (!is_array($tenantConfig)) {
            return null;
        }

        $databaseUrl = $tenantConfig['database_url'] ?? null;

        return is_string($databaseUrl) && trim($databaseUrl) !== '' ? trim($databaseUrl) : null;
    }
}
