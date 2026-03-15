<?php

declare(strict_types=1);

namespace App\Company\UI\Http\Controller;

use App\Company\Application\DTO\CreateEmpresaRequest;
use App\Company\Application\DTO\EmpresaResponse;
use App\Company\Application\DTO\UpdateEmpresaRequest;
use App\Company\Application\Service\EmpresaService;
use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/empresas')]
final class EmpresaController extends AbstractController
{
    public function __construct(
        private readonly EmpresaService $empresaService,
        private readonly UserCompanyRepositoryInterface $userCompanyRepository,
        private readonly JsonResponseFactory $responseFactory
    ) {
    }

    #[Route('', name: 'api_v1_empresas_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(User::ROLE_ROOT);
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new CreateEmpresaRequest();
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->razaoSocial = (string) ($payload['razaoSocial'] ?? '');
        $dto->nomeFantasia = isset($payload['nomeFantasia']) ? (string) $payload['nomeFantasia'] : null;
        $dto->cnpj = (string) ($payload['cnpj'] ?? '');
        $dto->status = (string) ($payload['status'] ?? 'active');

        $empresa = $this->empresaService->create($dto);

        return $this->responseFactory->success([
            'item' => EmpresaResponse::fromEntity($empresa),
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('', name: 'api_v1_empresas_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $companyId = $request->query->has('companyId') ? $request->query->getInt('companyId') : null;
        $status = $request->query->getString('status') ?: null;
        $user = $this->requireUser();

        if (!$user->isRoot()) {
            $companyIds = $this->userCompanyRepository->listCompanyIdsByUser((int) $user->getId());
            $items = [];

            foreach ($companyIds as $linkedCompanyId) {
                foreach ($this->empresaService->list($linkedCompanyId, $status) as $empresa) {
                    $items[] = $empresa;
                }
            }

            if ($companyId !== null) {
                $items = array_values(array_filter($items, static fn ($empresa): bool => (int) $empresa->getCompany()->getId() === $companyId));
            }

            return $this->responseFactory->success([
                'items' => array_map(
                    static fn ($empresa): array => EmpresaResponse::fromEntity($empresa),
                    $items
                ),
            ]);
        }

        return $this->responseFactory->success([
            'items' => array_map(
                static fn ($empresa): array => EmpresaResponse::fromEntity($empresa),
                $this->empresaService->list($companyId, $status)
            ),
        ]);
    }

    #[Route('/{id}', name: 'api_v1_empresas_get', methods: ['GET'])]
    public function getById(int $id): JsonResponse
    {
        $empresa = $this->empresaService->getById($id);
        $user = $this->requireUser();

        if (!$user->isRoot() && !$this->userCompanyRepository->userHasCompany((int) $user->getId(), (int) $empresa->getCompany()->getId())) {
            throw $this->createAccessDeniedException('Usuário não possui acesso à company da empresa informada.');
        }

        return $this->responseFactory->success([
            'item' => EmpresaResponse::fromEntity($empresa),
        ]);
    }

    #[Route('/{id}', name: 'api_v1_empresas_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(User::ROLE_ROOT);
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new UpdateEmpresaRequest();
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->razaoSocial = (string) ($payload['razaoSocial'] ?? '');
        $dto->nomeFantasia = isset($payload['nomeFantasia']) && $payload['nomeFantasia'] !== '' ? (string) $payload['nomeFantasia'] : null;
        $dto->cnpj = (string) ($payload['cnpj'] ?? '');
        $dto->status = (string) ($payload['status'] ?? 'active');

        $empresa = $this->empresaService->update($id, $dto);

        return $this->responseFactory->success([
            'item' => EmpresaResponse::fromEntity($empresa),
        ]);
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
