<?php

declare(strict_types=1);

namespace App\Cadastro\UI\Http\Controller;

use App\Cadastro\Application\DTO\CadastroResponses;
use App\Cadastro\Application\DTO\CreatePessoaEnderecoRequest;
use App\Cadastro\Application\DTO\UpdatePessoaEnderecoRequest;
use App\Cadastro\Application\Service\PessoaEnderecoService;
use App\Cadastro\Application\Service\PessoaService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Domain\Entity\User;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/pessoas/{pessoaId}/enderecos')]
final class PessoaEnderecoController extends AbstractController
{
    public function __construct(
        private readonly PessoaEnderecoService $service,
        private readonly PessoaService $pessoaService,
        private readonly JsonResponseFactory $responseFactory,
    ) {
    }

    #[Route('', name: 'api_v1_pessoa_enderecos_list', methods: ['GET'])]
    public function list(int $pessoaId): JsonResponse
    {
        $pessoa = $this->pessoaService->getById($pessoaId);
        $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cadastros.pessoas.view', (int) $pessoa->getCompany()->getId(), null));

        return $this->responseFactory->success([
            'items' => array_map(
                static fn ($e) => CadastroResponses::pessoaEndereco($e),
                $this->service->listByPessoa($pessoaId)
            ),
        ]);
    }

    #[Route('', name: 'api_v1_pessoa_enderecos_create', methods: ['POST'])]
    public function create(int $pessoaId, Request $request): JsonResponse
    {
        $pessoa = $this->pessoaService->getById($pessoaId);
        $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cadastros.pessoas.create_edit', (int) $pessoa->getCompany()->getId(), null));

        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new CreatePessoaEnderecoRequest();
        $dto->pessoaId = $pessoaId;
        $dto->tipoEndereco = (string) ($payload['tipoEndereco'] ?? 'Comercial');
        $dto->logradouro = (string) ($payload['logradouro'] ?? '');
        $dto->numero = $this->nullable($payload['numero'] ?? null);
        $dto->complemento = $this->nullable($payload['complemento'] ?? null);
        $dto->bairro = $this->nullable($payload['bairro'] ?? null);
        $dto->cidade = (string) ($payload['cidade'] ?? '');
        $dto->uf = $this->nullable($payload['uf'] ?? null);
        $dto->cep = $this->nullable($payload['cep'] ?? null);
        $dto->pais = (string) ($payload['pais'] ?? 'Brasil');
        $dto->principal = (bool) ($payload['principal'] ?? false);

        $currentUser = $this->getUser();
        $currentUserId = $currentUser instanceof User ? (int) $currentUser->getId() : null;

        $endereco = $this->service->create($dto, $currentUserId);

        return $this->responseFactory->success(['item' => CadastroResponses::pessoaEndereco($endereco)], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_v1_pessoa_enderecos_update', methods: ['PUT'])]
    public function update(int $pessoaId, int $id, Request $request): JsonResponse
    {
        $pessoa = $this->pessoaService->getById($pessoaId);
        $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cadastros.pessoas.create_edit', (int) $pessoa->getCompany()->getId(), null));

        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new UpdatePessoaEnderecoRequest();
        $dto->tipoEndereco = (string) ($payload['tipoEndereco'] ?? 'Comercial');
        $dto->logradouro = (string) ($payload['logradouro'] ?? '');
        $dto->numero = $this->nullable($payload['numero'] ?? null);
        $dto->complemento = $this->nullable($payload['complemento'] ?? null);
        $dto->bairro = $this->nullable($payload['bairro'] ?? null);
        $dto->cidade = (string) ($payload['cidade'] ?? '');
        $dto->uf = $this->nullable($payload['uf'] ?? null);
        $dto->cep = $this->nullable($payload['cep'] ?? null);
        $dto->pais = (string) ($payload['pais'] ?? 'Brasil');
        $dto->principal = (bool) ($payload['principal'] ?? false);

        $currentUser = $this->getUser();
        $currentUserId = $currentUser instanceof User ? (int) $currentUser->getId() : null;

        $updated = $this->service->update($id, $dto, $currentUserId);

        return $this->responseFactory->success(['item' => CadastroResponses::pessoaEndereco($updated)]);
    }

    #[Route('/{id}', name: 'api_v1_pessoa_enderecos_delete', methods: ['DELETE'])]
    public function delete(int $pessoaId, int $id): JsonResponse
    {
        $pessoa = $this->pessoaService->getById($pessoaId);
        $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cadastros.pessoas.create_edit', (int) $pessoa->getCompany()->getId(), null));

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
