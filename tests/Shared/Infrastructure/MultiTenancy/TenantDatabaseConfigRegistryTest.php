<?php

declare(strict_types=1);

namespace App\Tests\Shared\Infrastructure\MultiTenancy;

use App\Shared\Infrastructure\MultiTenancy\TenantDatabaseConfigRegistry;
use PHPUnit\Framework\TestCase;

final class TenantDatabaseConfigRegistryTest extends TestCase
{
    public function testHasDatabaseKeyAndFindDatabaseUrl(): void
    {
        $registry = new TenantDatabaseConfigRegistry([
            'shared_base_1' => [
                'name' => 'shared_base_1',
                'database_url' => 'mysql://shared/base1',
            ],
            'shared_base_2' => [
                'name' => 'shared_base_2',
                'database_url' => null,
            ],
        ]);

        self::assertTrue($registry->hasDatabaseKey('shared_base_1'));
        self::assertSame('mysql://shared/base1', $registry->findDatabaseUrl('shared_base_1'));
        self::assertTrue($registry->hasDatabaseKey('shared_base_2'));
        self::assertNull($registry->findDatabaseUrl('shared_base_2'));
        self::assertFalse($registry->hasDatabaseKey('missing_key'));
    }
}
