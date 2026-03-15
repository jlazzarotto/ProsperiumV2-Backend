<?php

declare(strict_types=1);

namespace App\Company\UI\Http\Controller;

use App\Company\Application\DTO\CompanyResponse;
use App\Company\Application\DTO\CreateCompanyRequest;
use App\Company\Application\DTO\UpdateCompanyRequest;
use App\Company\Application\Service\CompanyService;
use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/companies')]
final class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyService $companyService,
        private readonly UserCompanyRepositoryInterface $userCompanyRepository,
        private readonly JsonResponseFactory $responseFactory
    ) {
    }

    #[Route('', name: 'api_v1_companies_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(User::ROLE_ROOT);

        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new CreateCompanyRequest();
        $dto->nome = (string) ($payload['nome'] ?? '');
        $dto->tenancyMode = (string) ($payload['tenancyMode'] ?? 'shared');
        $dto->databaseKey = isset($payload['databaseKey']) && $payload['databaseKey'] !== '' ? (string) $payload['databaseKey'] : null;
        $dto->status = (string) ($payload['status'] ?? 'active');

        $result = $this->companyService->create($dto);

        return $this->responseFactory->success([
            'item' => CompanyResponse::fromEntity($result['company'], $result['tenantInstance']),
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('', name: 'api_v1_companies_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $status = $request->query->getString('status') ?: null;
        $items = $this->companyService->list($status);
        $user = $this->getUser();

        if (!$user instanceof User) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        }

        if (!$user->isRoot()) {
            $companyIds = $this->userCompanyRepository->listCompanyIdsByUser((int) $user->getId());
            $items = array_values(array_filter($items, static fn (array $item): bool => in_array((int) $item['company']->getId(), $companyIds, true)));
        }

        $items = array_map(
            static fn (array $item): array => CompanyResponse::fromEntity($item['company'], $item['tenantInstance']),
            $items
        );

        return $this->responseFactory->success([
            'items' => $items,
        ]);
    }

    #[Route('/{id}', name: 'api_v1_companies_get', methods: ['GET'])]
    public function getById(int $id): JsonResponse
    {
        $this->guardCompanyAccess($id);
        $result = $this->companyService->getById($id);

        return $this->responseFactory->success([
            'item' => CompanyResponse::fromEntity($result['company'], $result['tenantInstance']),
        ]);
    }

    #[Route('/{id}', name: 'api_v1_companies_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(User::ROLE_ROOT);

        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new UpdateCompanyRequest();
        $dto->nome = (string) ($payload['nome'] ?? '');
        $dto->tenancyMode = (string) ($payload['tenancyMode'] ?? 'shared');
        $dto->databaseKey = isset($payload['databaseKey']) && $payload['databaseKey'] !== '' ? (string) $payload['databaseKey'] : null;
        $dto->status = (string) ($payload['status'] ?? 'active');

        $result = $this->companyService->update($id, $dto);

        return $this->responseFactory->success([
            'item' => CompanyResponse::fromEntity($result['company'], $result['tenantInstance']),
        ]);
    }

    private function guardCompanyAccess(int $companyId): void
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        }

        if ($user->isRoot()) {
            return;
        }

        if (!$user->isAdmin() || !$this->userCompanyRepository->userHasCompany((int) $user->getId(), $companyId)) {
            throw $this->createAccessDeniedException('Usuário não possui acesso à company informada.');
        }
    }
}
