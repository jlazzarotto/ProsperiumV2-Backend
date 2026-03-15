<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MultiTenancy;

final class TenantContext
{
    private ?string $tenantId = null;
    private ?int $companyId = null;
    private ?string $tenancyMode = null;
    private ?string $databaseKey = null;
    private ?string $dedicatedDatabaseUrl = null;

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

    public function setCompanyId(?int $companyId): void
    {
        $this->companyId = $companyId;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function hasCompany(): bool
    {
        return $this->companyId !== null && $this->companyId > 0;
    }

    public function setTenancyMode(?string $tenancyMode): void
    {
        $this->tenancyMode = $tenancyMode !== null ? trim($tenancyMode) : null;
    }

    public function getTenancyMode(): ?string
    {
        return $this->tenancyMode;
    }

    public function setDatabaseKey(?string $databaseKey): void
    {
        $this->databaseKey = $databaseKey !== null ? trim($databaseKey) : null;
    }

    public function getDatabaseKey(): ?string
    {
        return $this->databaseKey;
    }

    public function setDedicatedDatabaseUrl(?string $dedicatedDatabaseUrl): void
    {
        $this->dedicatedDatabaseUrl = $dedicatedDatabaseUrl !== null ? trim($dedicatedDatabaseUrl) : null;
    }

    public function getDedicatedDatabaseUrl(): ?string
    {
        return $this->dedicatedDatabaseUrl;
    }

    public function isDedicated(): bool
    {
        return $this->tenancyMode === 'dedicated';
    }
}
