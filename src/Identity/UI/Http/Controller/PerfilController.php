<?php

declare(strict_types=1);

namespace App\Identity\UI\Http\Controller;

use App\Identity\Application\DTO\CreatePerfilRequest;
use App\Identity\Application\DTO\UpdatePerfilRequest;
use App\Identity\Application\DTO\PerfilResponse;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Application\Service\PerfilService;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/perfis')]
final class PerfilController extends AbstractController
{
    public function __construct(
        private readonly PerfilService $perfilService,
        private readonly JsonResponseFactory $responseFactory
    ) {
    }

    #[Route('', name: 'api_v1_perfis_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new CreatePerfilRequest();
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->codigo = (string) ($payload['codigo'] ?? '');
        $dto->nome = (string) ($payload['nome'] ?? '');
        $dto->permissionCodes = array_map('strval', $payload['permissionCodes'] ?? []);
        $dto->tipo = (string) ($payload['tipo'] ?? 'custom');
        $dto->status = (string) ($payload['status'] ?? 'active');

        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('identity.perfis.create', $dto->companyId)
        );

        $perfil = $this->perfilService->create($dto);

        return $this->responseFactory->success([
            'item' => PerfilResponse::fromEntity($perfil, $dto->permissionCodes),
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('', name: 'api_v1_perfis_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $companyId = $request->query->has('companyId') ? $request->query->getInt('companyId') : null;
        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.permissoes.view', $companyId)
        );

        return $this->responseFactory->success([
            'items' => array_map(
                static fn (array $item): array => PerfilResponse::fromEntity($item['perfil'], $item['permissionCodes']),
                $this->perfilService->list($companyId)
            ),
        ]);
    }

    #[Route('/{id}', name: 'api_v1_perfis_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new UpdatePerfilRequest();
        $dto->nome = (string) ($payload['nome'] ?? '');
        $dto->permissionCodes = array_map('strval', $payload['permissionCodes'] ?? []);
        $dto->tipo = (string) ($payload['tipo'] ?? 'custom');
        $dto->status = (string) ($payload['status'] ?? 'active');

        $this->denyAccessUnlessGranted(\App\Identity\Domain\Entity\User::ROLE_ROOT);

        $perfil = $this->perfilService->update($id, $dto);

        return $this->responseFactory->success([
            'item' => PerfilResponse::fromEntity($perfil, $dto->permissionCodes),
        ]);
    }
}
