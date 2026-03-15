<?php

declare(strict_types=1);

namespace App\Integracao\Psp\UI\Http\Controller;

use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Integracao\Psp\Application\DTO\PspHistoryResponses;
use App\Integracao\Psp\Application\DTO\PspResponses;
use App\Integracao\Psp\Application\Service\PspConsultaHistoricoService;
use App\Integracao\Psp\Application\Service\PspDuvService;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/integracoes/psp')]
final class PspController extends AbstractController
{
    public function __construct(
        private readonly PspDuvService $service,
        private readonly PspConsultaHistoricoService $historicoService,
        private readonly JsonResponseFactory $responseFactory,
    ) {
    }

    #[Route('/status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.importacao_dados.view'),
        );

        return $this->responseFactory->success([
            'psp' => $this->service->testAuthentication(),
        ]);
    }

    #[Route('/duvs', methods: ['GET'])]
    public function listDuvs(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.importacao_dados.view'),
        );

        $filters = [
            'imo' => $request->query->getString('imo') ?: null,
            'inscricao' => $request->query->getString('inscricao') ?: null,
            'situacaoDuv' => $request->query->getString('situacaoDuv') ?: null,
            'nomeEmbarcacao' => $request->query->getString('nomeEmbarcacao') ?: null,
            'natureza' => $request->query->getString('natureza') ?: null,
            'finalizado' => $request->query->has('finalizado') ? filter_var($request->query->get('finalizado'), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) : null,
            'pagina' => $request->query->has('pagina') ? $request->query->getInt('pagina') : null,
            'porto' => $request->query->getString('porto') ?: null,
            'retornarPendencia' => $request->query->has('retornarPendencia') ? filter_var($request->query->get('retornarPendencia'), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) : null,
        ];

        return $this->responseFactory->success([
            'items' => PspResponses::duvs($this->service->listDuvs($filters)),
        ]);
    }

    #[Route('/duvs/{numeroDuv}/resumo', methods: ['GET'])]
    public function resumo(int $numeroDuv): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.importacao_dados.view'),
        );

        return $this->responseFactory->success([
            'item' => PspResponses::resumo($this->service->getResumo($numeroDuv)),
        ]);
    }

    #[Route('/duvs/{numeroDuv}/embarcacao', methods: ['GET'])]
    public function embarcacao(int $numeroDuv): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.importacao_dados.view'),
        );

        return $this->responseFactory->success([
            'item' => PspResponses::embarcacao($this->service->getEmbarcacao($numeroDuv)),
        ]);
    }

    #[Route('/duvs/{numeroDuv}/anuencias', methods: ['GET'])]
    public function anuencias(int $numeroDuv): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.importacao_dados.view'),
        );

        return $this->responseFactory->success([
            'item' => PspResponses::anuencias($this->service->getAnuencias($numeroDuv)),
        ]);
    }

    #[Route('/duvs/{numeroDuv}/chegadas-saidas', methods: ['GET'])]
    public function chegadasSaidas(int $numeroDuv, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.importacao_dados.view'),
        );

        $v2 = !$request->query->has('v2') || filter_var($request->query->get('v2'), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) !== false;

        return $this->responseFactory->success([
            'item' => PspResponses::chegadasSaidas($this->service->getChegadasSaidas($numeroDuv, $v2)),
            'meta' => ['version' => $v2 ? 'v2' : 'v1'],
        ]);
    }

    #[Route('/duvs/{numeroDuv}/anexos', methods: ['GET'])]
    public function anexos(int $numeroDuv): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.importacao_dados.view'),
        );

        return $this->responseFactory->success([
            'items' => PspResponses::anexos($this->service->getAnexos($numeroDuv)),
        ]);
    }

    #[Route('/cadastro/portos/{bitrigramaPorto}/locais-atracacao', methods: ['GET'])]
    public function locaisAtracacao(string $bitrigramaPorto): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.importacao_dados.view'),
        );

        return $this->responseFactory->success([
            'items' => PspResponses::locaisAtracacao($this->service->getLocaisAtracacao($bitrigramaPorto)),
        ]);
    }

    #[Route('/historico', methods: ['GET'])]
    public function historico(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('admin.importacao_dados.view'),
        );

        $limit = $request->query->has('limit') ? max(1, min(100, $request->query->getInt('limit'))) : 30;
        $endpointKey = $request->query->getString('endpointKey') ?: null;
        $items = $this->historicoService->listRecent($endpointKey, $limit);

        return $this->responseFactory->success([
            'items' => array_map([PspHistoryResponses::class, 'item'], $items),
        ]);
    }
}
