<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MultiTenancy;

final class TenantConnectionRuntime
{
    private static ?string $databaseUrl = null;

    public static function setDatabaseUrl(?string $databaseUrl): void
    {
        self::$databaseUrl = $databaseUrl !== null ? trim($databaseUrl) : null;
    }

    public static function getDatabaseUrl(): ?string
    {
        return self::$databaseUrl;
    }

    public static function reset(): void
    {
        self::$databaseUrl = null;
    }
}
