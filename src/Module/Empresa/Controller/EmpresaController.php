<?php

declare(strict_types=1);

namespace App\Module\Empresa\Controller;

use App\Module\Empresa\Application\DTO\CreateEmpresaDTO;
use App\Module\Empresa\Application\DTO\UpdateEmpresaDTO;
use App\Module\Empresa\Application\Service\EmpresaService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/empresas')]
class EmpresaController
{
    public function __construct(private readonly EmpresaService $service)
    {
    }

    #[Route('', methods: ['GET'])]
    public function listar(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => [
                'items' => array_map(
                    static fn ($empresa): array => $empresa->toArray(),
                    $this->service->listar()
                ),
            ],
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function buscar(int $id): JsonResponse
    {
        $empresa = $this->service->buscar($id);

        if (!$empresa) {
            return new JsonResponse([
                'success' => false,
                'errors' => [['message' => 'Empresa não encontrada']],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'success' => true,
            'data' => $empresa->toArray(),
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function criar(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new CreateEmpresaDTO();
        $dto->razaoSocial = (string) ($data['razaoSocial'] ?? '');
        $dto->nomeFantasia = isset($data['nomeFantasia']) ? (string) $data['nomeFantasia'] : null;
        $dto->cnpj = isset($data['cnpj']) ? (string) $data['cnpj'] : null;

        $empresa = $this->service->criar($dto);

        return new JsonResponse([
            'success' => true,
            'data' => $empresa->toArray(),
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function atualizar(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new UpdateEmpresaDTO();
        $dto->razaoSocial = array_key_exists('razaoSocial', $data) ? (string) $data['razaoSocial'] : null;
        $dto->nomeFantasia = array_key_exists('nomeFantasia', $data) ? ($data['nomeFantasia'] !== null ? (string) $data['nomeFantasia'] : null) : null;
        $dto->cnpj = array_key_exists('cnpj', $data) ? ($data['cnpj'] !== null ? (string) $data['cnpj'] : null) : null;

        $empresa = $this->service->atualizar($id, $dto);

        if (!$empresa) {
            return new JsonResponse([
                'success' => false,
                'errors' => [['message' => 'Empresa não encontrada']],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'success' => true,
            'data' => $empresa->toArray(),
        ]);
    }

    #[Route('/{id}/inativar', methods: ['PATCH'])]
    public function inativar(int $id): JsonResponse
    {
        $result = $this->service->inativar($id);

        if (!$result) {
            return new JsonResponse([
                'success' => false,
                'errors' => [['message' => 'Empresa não encontrada']],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
