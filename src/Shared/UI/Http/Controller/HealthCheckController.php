<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Controller;

use App\Shared\Domain\Contract\TenantEntityManagerProviderInterface;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use App\Shared\Infrastructure\MultiTenancy\TenantContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthCheckController
{
    public function __construct(
        private readonly JsonResponseFactory $responseFactory,
        private readonly TenantContext $tenantContext,
        private readonly TenantEntityManagerProviderInterface $tenantEntityManagerProvider
    ) {
    }

    #[Route(path: '/api/v1/health', name: 'api_health_check', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return $this->responseFactory->success([
            'service' => 'prosperium-backend',
            'status' => 'ok',
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
            'controlPlane' => [
                'status' => 'ok',
                'description' => 'Autenticação, usuários, perfis, permissões, grupos econômicos, tenant_instances',
            ],
            'tenantPlane' => [
                'resolved' => $this->tenantEntityManagerProvider->isAvailable(),
                'tenancyMode' => $this->tenantContext->getTenancyMode(),
                'databaseKey' => $this->tenantContext->getDatabaseKey(),
                'databaseConfigured' => $this->tenantContext->getResolvedDatabaseUrl() !== null,
                'companyId' => $this->tenantContext->getCompanyId(),
            ],
        ]);
    }
}
