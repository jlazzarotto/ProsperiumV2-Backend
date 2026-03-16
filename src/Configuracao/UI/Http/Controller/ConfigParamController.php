<?php

declare(strict_types=1);

namespace App\Configuracao\UI\Http\Controller;

use App\Configuracao\Application\DTO\ConfigParamResponse;
use App\Configuracao\Application\DTO\SaveConfigParamRequest;
use App\Configuracao\Application\DTO\UpdateConfigParamRestrictRequest;
use App\Configuracao\Application\DTO\UpdateConfigParamStatusRequest;
use App\Configuracao\Application\Service\ConfigParamService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/config-params')]
final class ConfigParamController extends AbstractController
{
    public function __construct(
        private readonly ConfigParamService $service,
        private readonly JsonResponseFactory $responseFactory,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $companyId = $request->query->getInt('companyId');

        if ($companyId <= 0) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            return $this->responseFactory->error(['message' => 'companyId inválido ou não informado'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.parametrizacao_sistema.view', $companyId),
        );

        $result = $this->service->list($companyId);

        return $this->responseFactory->success([
            'data' => array_map(
                static fn($p) => ConfigParamResponse::fromEntity($p),
                $result['data'],
            ),
            'types' => $result['types'],
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function save(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new SaveConfigParamRequest();
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->name = (string) ($payload['name'] ?? '');
        $dto->value = (string) ($payload['value'] ?? '');
        $dto->type = isset($payload['type']) ? (string) $payload['type'] : null;
        $dto->description = isset($payload['description']) ? (string) $payload['description'] : null;
        $dto->originalName = isset($payload['original_name']) ? (string) $payload['original_name'] : null;

        if ($dto->companyId === null || $dto->companyId <= 0) {
            return $this->responseFactory->error(['message' => 'companyId inválido ou não informado'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.parametrizacao_sistema.create_edit', $dto->companyId),
        );

        $param = $this->service->save($dto);

        $isUpdate = ($dto->originalName !== null && trim($dto->originalName) !== '');

        return $this->responseFactory->success(
            ['item' => ConfigParamResponse::fromEntity($param)],
            $isUpdate ? JsonResponse::HTTP_OK : JsonResponse::HTTP_CREATED,
        );
    }

    #[Route('/status', methods: ['PUT'])]
    public function updateStatus(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new UpdateConfigParamStatusRequest();
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->name = (string) ($payload['name'] ?? '');
        $dto->status = isset($payload['status']) ? (int) $payload['status'] : null;

        if ($dto->companyId === null || $dto->companyId <= 0) {
            return $this->responseFactory->error(['message' => 'companyId inválido ou não informado'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.parametrizacao_sistema.create_edit', $dto->companyId),
        );

        $param = $this->service->updateStatus($dto);

        return $this->responseFactory->success([
            'item' => ConfigParamResponse::fromEntity($param),
        ]);
    }

    #[Route('/restrict', methods: ['PUT'])]
    public function updateRestrict(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new UpdateConfigParamRestrictRequest();
        $dto->companyId = isset($payload['companyId']) ? (int) $payload['companyId'] : null;
        $dto->name = (string) ($payload['name'] ?? '');
        $dto->restrict = isset($payload['restrict']) ? (int) $payload['restrict'] : null;

        if ($dto->companyId === null || $dto->companyId <= 0) {
            return $this->responseFactory->error(['message' => 'companyId inválido ou não informado'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.parametrizacao_sistema.create_edit', $dto->companyId),
        );

        $param = $this->service->updateRestrict($dto);

        return $this->responseFactory->success([
            'item' => ConfigParamResponse::fromEntity($param),
        ]);
    }
}
