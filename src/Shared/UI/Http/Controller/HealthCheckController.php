<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Controller;

use App\Shared\Infrastructure\Http\JsonResponseFactory;
use App\Shared\Infrastructure\MultiTenancy\TenantContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthCheckController
{
    public function __construct(
        private readonly JsonResponseFactory $responseFactory,
        private readonly TenantContext $tenantContext
    ) {
    }

    #[Route(path: '/api/v1/health', name: 'api_health_check', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return $this->responseFactory->success([
            'service' => 'prosperium-backend',
            'status' => 'ok',
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
            'tenantId' => $this->tenantContext->getTenantId(),
        ]);
    }
}
