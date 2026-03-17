<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Controller;

use App\Shared\Application\Service\ExecutiveDashboardService;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Dashboard Executivo - Dados agregados de todos os tenants
 * Acessível por ROLE_ROOT sem necessidade de company_id selecionado
 */
#[Route('/api/v1/dashboard')]
final class DashboardController extends AbstractController
{
    public function __construct(
        private readonly ExecutiveDashboardService $dashboardService,
        private readonly JsonResponseFactory $responseFactory
    ) {
    }

    #[Route('/summary', name: 'api_v1_dashboard_summary', methods: ['GET'])]
    public function getSummary(): JsonResponse
    {
        $metrics = $this->dashboardService->getAggregatedMetrics();

        return $this->responseFactory->success([
            'metrics' => $metrics,
        ]);
    }
}
