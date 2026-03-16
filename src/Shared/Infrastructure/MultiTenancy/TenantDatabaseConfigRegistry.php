<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MultiTenancy;

use App\Shared\Domain\Contract\TenantDatabaseRegistryInterface;

final class TenantDatabaseConfigRegistry implements TenantDatabaseRegistryInterface
{
    /**
     * @param array<string, mixed>|null $tenants
     */
    public function __construct(private readonly ?array $tenants = null)
    {
    }

    public function findDatabaseUrl(string $databaseKey): ?string
    {
        $tenantConfig = $this->findTenantConfig($databaseKey);

        if (!is_array($tenantConfig)) {
            return null;
        }

        $databaseUrl = $tenantConfig['database_url'] ?? null;

        return is_string($databaseUrl) && trim($databaseUrl) !== '' ? trim($databaseUrl) : null;
    }

    /**
     * @return list<array{key: string, type: string}>
     */
    public function listTenants(): array
    {
        if (!is_array($this->tenants)) {
            return [];
        }

        $result = [];
        foreach ($this->tenants as $key => $config) {
            $type = is_array($config) && isset($config['type']) ? (string) $config['type'] : 'shared';
            $result[] = ['key' => (string) $key, 'type' => $type];
        }

        return $result;
    }

    public function hasDatabaseKey(string $databaseKey): bool
    {
        return is_array($this->findTenantConfig($databaseKey));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findTenantConfig(string $databaseKey): ?array
    {
        $normalizedKey = trim($databaseKey);
        if ($normalizedKey === '') {
            return null;
        }

        $tenantConfig = $this->tenants[$normalizedKey] ?? null;

        return is_array($tenantConfig) ? $tenantConfig : null;
    }
}
