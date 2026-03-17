<?php

declare(strict_types=1);

namespace App\Identity\UI\Http\Controller;

use App\Identity\Application\DTO\UserResponse;
use App\Identity\Application\Service\AccessCatalogService;
use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Identity\Domain\Repository\UserEmpresaRepositoryInterface;
use App\Identity\Domain\Repository\UserPerfilRepositoryInterface;
use App\Identity\Domain\Repository\UserUnidadeRepositoryInterface;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class MeController extends AbstractController
{
    public function __construct(
        private readonly UserCompanyRepositoryInterface $userCompanyRepository,
        private readonly UserEmpresaRepositoryInterface $userEmpresaRepository,
        private readonly UserUnidadeRepositoryInterface $userUnidadeRepository,
        private readonly UserPerfilRepositoryInterface $userPerfilRepository,
        private readonly AccessCatalogService $accessCatalogService,
        private readonly JsonResponseFactory $responseFactory
    ) {
    }

    #[Route('/api/v1/me', name: 'api_v1_me', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->responseFactory->error([
                'message' => 'Usuário não autenticado.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // ROLE_ROOT: apenas dados de usuário (dashboard global, sem acesso a tenant DB)
        // Outros usuários: carregam dados da empresa/unidade/perfil do tenant DB
        $profileCodes = [];
        if (!$user->isRoot()) {
            $profileCodes = $this->userPerfilRepository->listProfileCodesByUser((int) $user->getId());
        }

        return $this->responseFactory->success([
            'item' => UserResponse::fromEntity(
                $user,
                $this->userCompanyRepository->listCompanyIdsByUser((int) $user->getId()),
                $this->userEmpresaRepository->listEmpresaIdsByUser((int) $user->getId()),
                $this->userUnidadeRepository->listUnidadeIdsByUser((int) $user->getId()),
                $profileCodes,
                $this->accessCatalogService->buildEnabledModules(),
                $this->accessCatalogService->buildPermissionMatrix($user),
                $this->accessCatalogService->buildMenu($user)
            ),
        ]);
    }
}
