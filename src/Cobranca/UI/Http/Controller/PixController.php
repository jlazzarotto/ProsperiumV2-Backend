<?php
declare(strict_types=1);
namespace App\Cobranca\UI\Http\Controller;
use App\Cobranca\Application\DTO\CobrancaResponses;
use App\Cobranca\Application\DTO\CreatePixCobrancaRequest;
use App\Cobranca\Application\DTO\RegisterPixWebhookRequest;
use App\Cobranca\Application\Service\PixCobrancaService;
use App\Cobranca\Application\Service\PixWebhookService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
final class PixController extends AbstractController
{
    public function __construct(private readonly PixCobrancaService $pixService, private readonly PixWebhookService $webhookService, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/api/v1/pix/cobrancas', methods: ['POST'])]
    public function create(Request $request): JsonResponse { $p = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR); $dto = new CreatePixCobrancaRequest(); $dto->companyId = isset($p['companyId']) ? (int) $p['companyId'] : null; $dto->empresaId = isset($p['empresaId']) ? (int) $p['empresaId'] : null; $dto->unidadeId = isset($p['unidadeId']) ? (int) $p['unidadeId'] : null; $dto->parcelaId = isset($p['parcelaId']) ? (int) $p['parcelaId'] : null; $dto->contaFinanceiraId = isset($p['contaFinanceiraId']) ? (int) $p['contaFinanceiraId'] : null; $dto->chavePix = (string) ($p['chavePix'] ?? ''); $dto->valor = (string) ($p['valor'] ?? '0.00'); $dto->expiracaoSegundos = isset($p['expiracaoSegundos']) ? (int) $p['expiracaoSegundos'] : 3600; $dto->txid = isset($p['txid']) ? (string) $p['txid'] : null; $dto->qrCode = isset($p['qrCode']) ? (string) $p['qrCode'] : null; $dto->copiaCola = isset($p['copiaCola']) ? (string) $p['copiaCola'] : null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cobranca.pix.cobrancas.create', $dto->companyId, $dto->empresaId, $dto->unidadeId)); $pix = $this->pixService->create($dto); return $this->responseFactory->success(['item' => CobrancaResponses::pixCobranca($pix)], JsonResponse::HTTP_CREATED); }
    #[Route('/api/v1/pix/cobrancas', methods: ['GET'])]
    public function list(Request $request): JsonResponse { $companyId = $request->query->getInt('companyId'); $empresaId = $request->query->has('empresaId') ? $request->query->getInt('empresaId') : null; $unidadeId = $request->query->has('unidadeId') ? $request->query->getInt('unidadeId') : null; $status = $request->query->get('status'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE, new PermissionContext('cobranca.pix.cobrancas.view', $companyId, $empresaId, $unidadeId)); $items = $this->pixService->list($companyId, $empresaId, $unidadeId, is_string($status) ? $status : null); return $this->responseFactory->success(['items' => array_map([CobrancaResponses::class, 'pixCobranca'], $items)]); }
    #[Route('/api/v1/pix/webhooks', methods: ['POST'])]
    public function webhook(Request $request): JsonResponse { $p = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR); $dto = new RegisterPixWebhookRequest(); $dto->companyId = isset($p['companyId']) ? (int) $p['companyId'] : null; $dto->empresaId = isset($p['empresaId']) ? (int) $p['empresaId'] : null; $dto->unidadeId = isset($p['unidadeId']) ? (int) $p['unidadeId'] : null; $dto->pixCobrancaId = isset($p['pixCobrancaId']) ? (int) $p['pixCobrancaId'] : null; $dto->txid = isset($p['txid']) ? (string) $p['txid'] : null; $dto->tipoEvento = (string) ($p['tipoEvento'] ?? ''); $dto->payload = is_array($p['payload'] ?? null) ? $p['payload'] : []; $dto->endToEndId = isset($p['endToEndId']) ? (string) $p['endToEndId'] : null; $dto->valor = isset($p['valor']) ? (string) $p['valor'] : null; $dto->recebidoEm = isset($p['recebidoEm']) ? (string) $p['recebidoEm'] : null; $evento = $this->webhookService->register($dto); return $this->responseFactory->success(['item' => CobrancaResponses::webhook($evento)], JsonResponse::HTTP_CREATED); }
}
