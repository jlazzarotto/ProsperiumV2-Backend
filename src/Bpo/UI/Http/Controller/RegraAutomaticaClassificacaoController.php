<?php
declare(strict_types=1);
namespace App\Bpo\UI\Http\Controller;
use App\Bpo\Application\DTO\BpoResponses;
use App\Bpo\Application\DTO\CreateRegraAutomaticaClassificacaoRequest;
use App\Bpo\Application\Service\RegraAutomaticaClassificacaoService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
final class RegraAutomaticaClassificacaoController extends AbstractController
{
    public function __construct(private readonly RegraAutomaticaClassificacaoService $service, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/api/v1/regras-automaticas-classificacao', methods: ['POST'])] public function create(Request $request): JsonResponse { $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateRegraAutomaticaClassificacaoRequest(); $dto->companyId=isset($p['companyId'])?(int)$p['companyId']:null; $dto->empresaId=isset($p['empresaId'])?(int)$p['empresaId']:null; $dto->unidadeId=isset($p['unidadeId'])?(int)$p['unidadeId']:null; $dto->categoriaFinanceiraId=isset($p['categoriaFinanceiraId'])?(int)$p['categoriaFinanceiraId']:null; $dto->centroCustoId=isset($p['centroCustoId'])?(int)$p['centroCustoId']:null; $dto->descricaoContains=(string)($p['descricaoContains']??''); $dto->acaoNotificacao=(bool)($p['acaoNotificacao']??true); $dto->status=(string)($p['status']??'active'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('bpo.regras_automaticas.create',$dto->companyId,$dto->empresaId,$dto->unidadeId)); $regra=$this->service->create($dto); return $this->responseFactory->success(['item'=>BpoResponses::regra($regra)],JsonResponse::HTTP_CREATED); }
}
