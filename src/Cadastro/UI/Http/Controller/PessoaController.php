<?php

declare(strict_types=1);

namespace App\Cadastro\UI\Http\Controller;

use App\Cadastro\Application\DTO\CadastroResponses;
use App\Cadastro\Application\DTO\CreatePessoaRequest;
use App\Cadastro\Application\DTO\UpdatePessoaRequest;
use App\Cadastro\Application\Service\PessoaService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Domain\Entity\User;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/pessoas')]
final class PessoaController extends AbstractController
{
    public function __construct(
        private readonly PessoaService $service,
        private readonly JsonResponseFactory $responseFactory,
    ) {
    }

    #[Route('', name: 'api_v1_pessoas_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $companyId = $request->query->getInt('companyId');
        $tipoPessoa = $request->query->getString('tipoPessoa') ?: null;
        $status = $request->query->getString('status') ?: null;

        $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cadastros.pessoas.view', $companyId, null));

        return $this->responseFactory->success([
            'items' => array_map(
                static fn ($p) => CadastroResponses::pessoa($p),
                $this->service->list($companyId, $tipoPessoa, $status)
            ),
        ]);
    }

    #[Route('/{id}', name: 'api_v1_pessoas_get', methods: ['GET'])]
    public function getById(int $id): JsonResponse
    {
        $pessoa = $this->service->getById($id);
        $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cadastros.pessoas.view', (int) $pessoa->getCompany()->getId(), null));

        return $this->responseFactory->success(['item' => CadastroResponses::pessoa($pessoa)]);
    }

    #[Route('', name: 'api_v1_pessoas_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new CreatePessoaRequest();
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->tipoPessoa = strtoupper((string) ($payload['tipoPessoa'] ?? 'PF'));
        $dto->nomeRazao = (string) ($payload['nomeRazao'] ?? '');
        $dto->nomeFantasia = $this->nullable($payload['nomeFantasia'] ?? null);
        $dto->documento = $this->nullable($payload['documento'] ?? null);
        $dto->inscricaoEstadual = $this->nullable($payload['inscricaoEstadual'] ?? null);
        $dto->emailPrincipal = $this->nullable($payload['emailPrincipal'] ?? null);
        $dto->telefonePrincipal = $this->nullable($payload['telefonePrincipal'] ?? null);
        $dto->status = (string) ($payload['status'] ?? 'active');

        $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cadastros.pessoas.create_edit', $dto->companyId, null));

        $currentUser = $this->getUser();
        $currentUserId = $currentUser instanceof User ? (int) $currentUser->getId() : null;

        $pessoa = $this->service->create($dto, $currentUserId);

        return $this->responseFactory->success(['item' => CadastroResponses::pessoa($pessoa)], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_v1_pessoas_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $pessoa = $this->service->getById($id);
        $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cadastros.pessoas.create_edit', (int) $pessoa->getCompany()->getId(), null));

        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new UpdatePessoaRequest();
        $dto->tipoPessoa = strtoupper((string) ($payload['tipoPessoa'] ?? 'PF'));
        $dto->nomeRazao = (string) ($payload['nomeRazao'] ?? '');
        $dto->nomeFantasia = $this->nullable($payload['nomeFantasia'] ?? null);
        $dto->documento = $this->nullable($payload['documento'] ?? null);
        $dto->inscricaoEstadual = $this->nullable($payload['inscricaoEstadual'] ?? null);
        $dto->emailPrincipal = $this->nullable($payload['emailPrincipal'] ?? null);
        $dto->telefonePrincipal = $this->nullable($payload['telefonePrincipal'] ?? null);
        $dto->status = (string) ($payload['status'] ?? 'active');

        $currentUser = $this->getUser();
        $currentUserId = $currentUser instanceof User ? (int) $currentUser->getId() : null;

        $updated = $this->service->update($id, $dto, $currentUserId);

        return $this->responseFactory->success(['item' => CadastroResponses::pessoa($updated)]);
    }

    #[Route('/{id}', name: 'api_v1_pessoas_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $pessoa = $this->service->getById($id);
        $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cadastros.pessoas.delete', (int) $pessoa->getCompany()->getId(), null));

        $currentUser = $this->getUser();
        $currentUserId = $currentUser instanceof User ? (int) $currentUser->getId() : null;

        $this->service->delete($id, $currentUserId);

        return $this->responseFactory->success([]);
    }

    private function nullable(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }
}
