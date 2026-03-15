<?php

declare(strict_types=1);

namespace App\Identity\UI\Http\Controller;

use App\Identity\Application\Service\AccessCatalogService;
use App\Identity\Domain\Entity\User;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/access')]
final class AccessController extends AbstractController
{
    public function __construct(
        private readonly AccessCatalogService $accessCatalogService,
        private readonly JsonResponseFactory $responseFactory
    ) {
    }

    #[Route('/menu', name: 'api_v1_access_menu', methods: ['GET'])]
    public function menu(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->responseFactory->error([
                'message' => 'Usuário não autenticado.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->responseFactory->success([
            'items' => $this->accessCatalogService->buildMenu($user),
        ]);
    }

    #[Route('/modules', name: 'api_v1_access_modules', methods: ['GET'])]
    public function modules(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->responseFactory->error([
                'message' => 'Usuário não autenticado.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->responseFactory->success([
            'items' => $this->accessCatalogService->listModulesCatalog(),
        ]);
    }
}
