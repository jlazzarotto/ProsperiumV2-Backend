<?php

declare(strict_types=1);

namespace App\Identity\UI\Http\Controller;

use App\Identity\Application\DTO\CreateUserRequest;
use App\Identity\Application\DTO\UpdateUserProfilesRequest;
use App\Identity\Application\DTO\UserResponse;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Application\Service\AccessCatalogService;
use App\Identity\Application\Service\UserService;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Identity\Domain\Repository\UserEmpresaRepositoryInterface;
use App\Identity\Domain\Repository\UserPerfilRepositoryInterface;
use App\Identity\Domain\Repository\UserUnidadeRepositoryInterface;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/users')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserCompanyRepositoryInterface $userCompanyRepository,
        private readonly UserEmpresaRepositoryInterface $userEmpresaRepository,
        private readonly UserUnidadeRepositoryInterface $userUnidadeRepository,
        private readonly UserPerfilRepositoryInterface $userPerfilRepository,
        private readonly AccessCatalogService $accessCatalogService,
        private readonly JsonResponseFactory $responseFactory
    ) {
    }

    #[Route('', name: 'api_v1_users_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new CreateUserRequest();
        $dto->nome = (string) ($payload['nome'] ?? '');
        $dto->email = (string) ($payload['email'] ?? '');
        $dto->password = (string) ($payload['password'] ?? '');
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->empresaIds = array_map('intval', $payload['empresaIds'] ?? []);
        $dto->unidadeIds = array_map('intval', $payload['unidadeIds'] ?? []);
        $dto->profileCodes = array_map('strval', $payload['profileCodes'] ?? []);
        $dto->role = isset($payload['role']) ? (string) $payload['role'] : \App\Identity\Domain\Entity\User::ROLE_ADMIN;
        $dto->isCompanyAdmin = $dto->role === \App\Identity\Domain\Entity\User::ROLE_ADMIN;
        $dto->status = (string) ($payload['status'] ?? 'active');

        if ($this->userService->hasUsers()) {
            $this->denyAccessUnlessGranted(\App\Identity\Domain\Entity\User::ROLE_ROOT);
        }

        $user = $this->userService->create($dto);

        return $this->responseFactory->success([
            'item' => UserResponse::fromEntity(
                $user,
                $this->userCompanyRepository->listCompanyIdsByUser((int) $user->getId()),
                $this->userEmpresaRepository->listEmpresaIdsByUser((int) $user->getId(), $dto->companyId),
                $this->userUnidadeRepository->listUnidadeIdsByUser((int) $user->getId(), $dto->companyId),
                $this->userPerfilRepository->listProfileCodesByUser((int) $user->getId(), $dto->companyId),
                $this->accessCatalogService->buildEnabledModules(),
                $this->accessCatalogService->buildPermissionMatrix($user),
                $this->accessCatalogService->buildMenu($user)
            ),
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('', name: 'api_v1_users_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $companyId = $request->query->has('companyId') ? $request->query->getInt('companyId') : null;
        $this->denyAccessUnlessGranted(\App\Identity\Domain\Entity\User::ROLE_ROOT);

        $items = array_map(function ($user) use ($companyId): array {
            return UserResponse::fromEntity(
                $user,
                $this->userCompanyRepository->listCompanyIdsByUser((int) $user->getId()),
                $this->userEmpresaRepository->listEmpresaIdsByUser((int) $user->getId(), $companyId),
                $this->userUnidadeRepository->listUnidadeIdsByUser((int) $user->getId(), $companyId),
                $this->userPerfilRepository->listProfileCodesByUser((int) $user->getId(), $companyId),
                $this->accessCatalogService->buildEnabledModules(),
                $this->accessCatalogService->buildPermissionMatrix($user),
                $this->accessCatalogService->buildMenu($user)
            );
        }, $this->userService->list($companyId));

        return $this->responseFactory->success([
            'items' => $items,
        ]);
    }

    #[Route('/{id}/profiles', name: 'api_v1_users_profiles_update', methods: ['PUT'])]
    public function updateProfiles(int $id, Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $dto = new UpdateUserProfilesRequest();
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->profileCodes = array_map('strval', $payload['profileCodes'] ?? []);

        $this->denyAccessUnlessGranted(\App\Identity\Domain\Entity\User::ROLE_ROOT);

        $user = $this->userService->updateProfiles($id, $dto);

        return $this->responseFactory->success([
            'item' => UserResponse::fromEntity(
                $user,
                $this->userCompanyRepository->listCompanyIdsByUser((int) $user->getId()),
                $this->userEmpresaRepository->listEmpresaIdsByUser((int) $user->getId(), $dto->companyId),
                $this->userUnidadeRepository->listUnidadeIdsByUser((int) $user->getId(), $dto->companyId),
                $this->userPerfilRepository->listProfileCodesByUser((int) $user->getId(), $dto->companyId),
                $this->accessCatalogService->buildEnabledModules(),
                $this->accessCatalogService->buildPermissionMatrix($user),
                $this->accessCatalogService->buildMenu($user)
            ),
        ]);
    }
}
