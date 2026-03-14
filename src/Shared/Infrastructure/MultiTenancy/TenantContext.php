<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MultiTenancy;

final class TenantContext
{
    private ?string $tenantId = null;

    public function setTenantId(?string $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function hasTenant(): bool
    {
        return $this->tenantId !== null && $this->tenantId !== '';
    }
}
