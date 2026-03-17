<?php

declare(strict_types=1);

namespace App\Company\UI\Http\Controller;

use App\Company\Application\DTO\CreateUnidadeNegocioRequest;
use App\Company\Application\DTO\UpdateUnidadeNegocioRequest;
use App\Company\Application\DTO\UnidadeNegocioResponse;
use App\Company\Application\Service\UnidadeNegocioService;
use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/unidades')]
final class UnidadeNegocioController extends AbstractController
{
    public function __construct(
        private readonly UnidadeNegocioService $unidadeNegocioService,
        private readonly UserCompanyRepositoryInterface $userCompanyRepository,
        private readonly JsonResponseFactory $responseFactory
    ) {
    }

    #[Route('', name: 'api_v1_unidades_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new CreateUnidadeNegocioRequest();
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->nome = (string) ($payload['nome'] ?? '');
        $dto->abreviatura = (string) ($payload['abreviatura'] ?? '');
        $dto->status = (string) ($payload['status'] ?? 'active');
        $this->guardCompanyAccess((int) $dto->companyId);

        $unidade = $this->unidadeNegocioService->create($dto);

        return $this->responseFactory->success([
            'item' => UnidadeNegocioResponse::fromEntity($unidade),
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('', name: 'api_v1_unidades_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $companyId = $request->query->has('companyId') ? $request->query->getInt('companyId') : null;
        $status = $request->query->getString('status') ?: null;
        $user = $this->requireUser();

        if (!$user->isRoot()) {
            $companyIds = $this->userCompanyRepository->listCompanyIdsByUser((int) $user->getId());
            $items = [];

            foreach ($companyIds as $linkedCompanyId) {
                foreach ($this->unidadeNegocioService->list($linkedCompanyId, $status) as $unidade) {
                    $items[] = $unidade;
                }
            }

            if ($companyId !== null) {
                $items = array_values(array_filter($items, static fn ($unidade): bool => (int) $unidade->getCompanyId() === $companyId));
            }

            return $this->responseFactory->success([
                'items' => array_map(
                    static fn ($unidade): array => UnidadeNegocioResponse::fromEntity($unidade),
                    $items
                ),
            ]);
        }

        return $this->responseFactory->success([
            'items' => array_map(
                static fn ($unidade): array => UnidadeNegocioResponse::fromEntity($unidade),
                $this->unidadeNegocioService->list($companyId, $status)
            ),
        ]);
    }

    #[Route('/{id}', name: 'api_v1_unidades_get', methods: ['GET'])]
    public function getById(int $id): JsonResponse
    {
        $unidade = $this->unidadeNegocioService->getById($id);
        $this->guardCompanyAccess((int) $unidade->getCompanyId());

        return $this->responseFactory->success([
            'item' => UnidadeNegocioResponse::fromEntity($unidade),
        ]);
    }

    #[Route('/{id}', name: 'api_v1_unidades_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new UpdateUnidadeNegocioRequest();
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->nome = (string) ($payload['nome'] ?? '');
        $dto->abreviatura = (string) ($payload['abreviatura'] ?? '');
        $dto->status = (string) ($payload['status'] ?? 'active');
        $this->guardCompanyAccess((int) $dto->companyId);

        $unidade = $this->unidadeNegocioService->update($id, $dto);

        return $this->responseFactory->success([
            'item' => UnidadeNegocioResponse::fromEntity($unidade),
        ]);
    }

    private function guardCompanyAccess(int $companyId): void
    {
        $user = $this->requireUser();

        if ($user->isRoot()) {
            return;
        }

        if (!$user->isAdmin() || !$this->userCompanyRepository->userHasCompany((int) $user->getId(), $companyId)) {
            throw $this->createAccessDeniedException('Usuário não possui acesso à company informada.');
        }
    }

    private function requireUser(): User
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        }

        return $user;
    }
}
