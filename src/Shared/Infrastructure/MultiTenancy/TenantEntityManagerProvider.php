<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MultiTenancy;

use App\Shared\Domain\Contract\TenantEntityManagerProviderInterface;
use Doctrine\ORM\EntityManagerInterface;

final class TenantEntityManagerProvider implements TenantEntityManagerProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $tenantEntityManager,
        private readonly TenantContext $tenantContext
    ) {
    }

    public function getEntityManager(): EntityManagerInterface
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException(
                'Tenant plane indisponível: nenhum databaseKey resolvido para o request atual. '
                . 'Verifique se o header X-Company-Id ou X-Tenant-Id está presente e válido.'
            );
        }

        return $this->tenantEntityManager;
    }

    public function isAvailable(): bool
    {
        return $this->tenantContext->getResolvedDatabaseUrl() !== null
            && $this->tenantContext->getDatabaseKey() !== null;
    }
}
