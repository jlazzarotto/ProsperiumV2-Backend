<?php
declare(strict_types=1);
namespace App\Cobranca\UI\Http\Controller;
use App\Cobranca\Application\DTO\CobrancaResponses;
use App\Cobranca\Application\DTO\CreateBoletoRemessaRequest;
use App\Cobranca\Application\DTO\ImportarBoletoRetornoRequest;
use App\Cobranca\Application\Service\BoletoRemessaService;
use App\Cobranca\Application\Service\BoletoRetornoService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
final class BoletoController extends AbstractController
{
    public function __construct(private readonly BoletoRemessaService $remessaService, private readonly BoletoRetornoService $retornoService, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/api/v1/boletos/remessas', methods: ['POST'])]
    public function createRemessa(Request $request): JsonResponse { $p = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR); $dto = new CreateBoletoRemessaRequest(); $dto->companyId = isset($p['companyId']) ? (int) $p['companyId'] : null; $dto->empresaId = isset($p['empresaId']) ? (int) $p['empresaId'] : null; $dto->unidadeId = isset($p['unidadeId']) ? (int) $p['unidadeId'] : null; $dto->contaFinanceiraId = isset($p['contaFinanceiraId']) ? (int) $p['contaFinanceiraId'] : null; $dto->banco = (string) ($p['banco'] ?? ''); $dto->parcelaIds = array_map('intval', $p['parcelaIds'] ?? []); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cobranca.boletos.remessas.create', $dto->companyId, $dto->empresaId, $dto->unidadeId)); $result = $this->remessaService->create($dto); return $this->responseFactory->success(['item' => CobrancaResponses::remessa($result['remessa'], $result['itens'])], JsonResponse::HTTP_CREATED); }
    #[Route('/api/v1/boletos/retornos/importar', methods: ['POST'])]
    public function importRetorno(Request $request): JsonResponse { $p = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR); $dto = new ImportarBoletoRetornoRequest(); $dto->companyId = isset($p['companyId']) ? (int) $p['companyId'] : null; $dto->remessaId = isset($p['remessaId']) ? (int) $p['remessaId'] : null; $dto->itens = is_array($p['itens'] ?? null) ? $p['itens'] : []; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cobranca.boletos.retornos.import', $dto->companyId)); $itens = $this->retornoService->import($dto); return $this->responseFactory->success(['items' => array_map([CobrancaResponses::class, 'retornoItem'], $itens)], JsonResponse::HTTP_CREATED); }
}
