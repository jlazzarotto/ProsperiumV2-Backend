<?php

declare(strict_types=1);

namespace App\Identity\UI\Http\Controller;

use App\Identity\Application\DTO\CreateUserRequest;
use App\Identity\Application\DTO\UpdateUserProfilesRequest;
use App\Identity\Application\DTO\UpdateUserRequest;
use App\Identity\Application\DTO\UserResponse;
use App\Identity\Application\Service\AccessCatalogService;
use App\Identity\Application\Service\UserService;
use App\Identity\Domain\Entity\User;
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
        $dto->role = isset($payload['role']) ? (string) $payload['role'] : User::ROLE_ADMIN;
        $dto->isCompanyAdmin = $dto->role === User::ROLE_ADMIN;
        $dto->status = (string) ($payload['status'] ?? User::STATUS_ATIVO);
        $dto->mfaHabilitado = (bool) ($payload['mfaHabilitado'] ?? false);

        if ($this->userService->hasUsers()) {
            $this->denyAccessUnlessGranted(User::ROLE_ROOT);
        }

        $user = $this->userService->create($dto);

        return $this->responseFactory->success([
            'item' => $this->buildUserResponse($user, $dto->companyId),
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('', name: 'api_v1_users_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->requireRootOrAdmin();

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $companyId = $request->query->has('companyId') ? $request->query->getInt('companyId') : null;
        $status = $request->query->has('status') ? $request->query->getString('status') : null;

        // ROLE_ADMIN só vê usuários da própria company
        if ($currentUser->isAdmin() && $companyId === null) {
            $companyId = $currentUser->getCompany()?->getId() !== null
                ? (int) $currentUser->getCompanyId()
                : null;
        }

        $items = array_map(
            fn (User $user): array => $this->buildUserResponse($user, $companyId),
            $this->userService->list($companyId, $status)
        );

        return $this->responseFactory->success([
            'items' => $items,
        ]);
    }

    #[Route('/{id}', name: 'api_v1_users_get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getById(int $id): JsonResponse
    {
        $this->requireRootOrAdmin();

        $user = $this->userService->getById($id);

        $this->guardCompanyAccess($user);

        $companyId = $user->getCompany()?->getId();

        return $this->responseFactory->success([
            'item' => $this->buildUserResponse($user, $companyId !== null ? (int) $companyId : null),
        ]);
    }

    #[Route('/{id}', name: 'api_v1_users_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $this->requireRootOrAdmin();

        $targetUser = $this->userService->getById($id);
        $this->guardCompanyAccess($targetUser);

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new UpdateUserRequest();
        $dto->nome = (string) ($payload['nome'] ?? '');
        $dto->email = (string) ($payload['email'] ?? '');
        $dto->password = isset($payload['password']) && $payload['password'] !== '' ? (string) $payload['password'] : null;
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->empresaIds = array_map('intval', $payload['empresaIds'] ?? []);
        $dto->unidadeIds = array_map('intval', $payload['unidadeIds'] ?? []);
        $dto->profileCodes = array_map('strval', $payload['profileCodes'] ?? []);
        $dto->status = (string) ($payload['status'] ?? User::STATUS_ATIVO);
        $dto->mfaHabilitado = (bool) ($payload['mfaHabilitado'] ?? false);

        // ROLE_ADMIN não pode alterar role nem promover para ROOT
        if ($currentUser->isAdmin()) {
            $dto->role = $targetUser->getRole();
        } else {
            $dto->role = isset($payload['role']) ? (string) $payload['role'] : $targetUser->getRole();
        }

        $user = $this->userService->update($id, $dto);

        return $this->responseFactory->success([
            'item' => $this->buildUserResponse($user, $dto->companyId),
        ]);
    }

    #[Route('/{id}', name: 'api_v1_users_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted(User::ROLE_ROOT);

        $this->userService->delete($id);

        return $this->responseFactory->success([
            'message' => 'Usuario inativado com sucesso.',
        ]);
    }

    #[Route('/{id}/desbloquear', name: 'api_v1_users_desbloquear', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function desbloquear(int $id): JsonResponse
    {
        $this->requireRootOrAdmin();

        $targetUser = $this->userService->getById($id);
        $this->guardCompanyAccess($targetUser);

        $user = $this->userService->desbloquear($id);
        $companyId = $user->getCompany()?->getId();

        return $this->responseFactory->success([
            'item' => $this->buildUserResponse($user, $companyId !== null ? (int) $companyId : null),
        ]);
    }

    #[Route('/{id}/profiles', name: 'api_v1_users_profiles_update', methods: ['PUT'])]
    public function updateProfiles(int $id, Request $request): JsonResponse
    {
        $this->requireRootOrAdmin();

        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $dto = new UpdateUserProfilesRequest();
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->profileCodes = array_map('strval', $payload['profileCodes'] ?? []);

        $targetUser = $this->userService->getById($id);
        $this->guardCompanyAccess($targetUser);

        $user = $this->userService->updateProfiles($id, $dto);

        return $this->responseFactory->success([
            'item' => $this->buildUserResponse($user, $dto->companyId),
        ]);
    }

    private function buildUserResponse(User $user, ?int $companyId): array
    {
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
    }

    /**
     * Exige que o usuário autenticado seja ROLE_ROOT ou ROLE_ADMIN.
     */
    private function requireRootOrAdmin(): void
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if ($user === null || (!$user->isRoot() && !$user->isAdmin())) {
            throw $this->createAccessDeniedException('Acesso restrito a ROLE_ROOT ou ROLE_ADMIN.');
        }
    }

    /**
     * ROLE_ADMIN só pode acessar usuários da mesma company.
     */
    private function guardCompanyAccess(User $targetUser): void
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->isRoot()) {
            return;
        }

        $currentCompanyId = $currentUser->getCompany()?->getId();
        $targetCompanyId = $targetUser->getCompany()?->getId();

        if ($currentCompanyId === null || $targetCompanyId === null || $currentCompanyId !== $targetCompanyId) {
            throw $this->createAccessDeniedException('Voce nao tem permissao para acessar este usuario.');
        }
    }
}
