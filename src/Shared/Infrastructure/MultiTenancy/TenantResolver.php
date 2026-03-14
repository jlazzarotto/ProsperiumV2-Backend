<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MultiTenancy;

use Symfony\Component\HttpFoundation\Request;

final class TenantResolver
{
    public function resolve(Request $request): ?string
    {
        return $request->headers->get('X-Tenant-Id');
    }
}
