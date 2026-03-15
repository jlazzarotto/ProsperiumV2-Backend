<?php

declare(strict_types=1);

namespace App\Identity\UI\Http\Controller;

use App\Identity\Application\DTO\PermissaoResponse;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Application\Service\PermissaoService;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/permissoes')]
final class PermissaoController extends AbstractController
{
    public function __construct(
        private readonly PermissaoService $permissaoService,
        private readonly JsonResponseFactory $responseFactory
    ) {
    }

    #[Route('', name: 'api_v1_permissoes_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $companyId = $request->query->has('companyId') ? $request->query->getInt('companyId') : null;
        $modulo = $request->query->getString('modulo') ?: null;

        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('identity.permissoes.view', $companyId)
        );

        return $this->responseFactory->success([
            'items' => array_map(
                static fn ($permissao): array => PermissaoResponse::fromEntity($permissao),
                $this->permissaoService->list($modulo)
            ),
        ]);
    }
}
