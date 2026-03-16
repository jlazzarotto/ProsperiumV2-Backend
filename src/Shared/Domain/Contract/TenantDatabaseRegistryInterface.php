<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contract;

interface TenantDatabaseRegistryInterface
{
    public function hasDatabaseKey(string $databaseKey): bool;

    public function findDatabaseUrl(string $databaseKey): ?string;

    /**
     * @return list<array{key: string, type: string}>
     */
    public function listTenants(): array;
}
