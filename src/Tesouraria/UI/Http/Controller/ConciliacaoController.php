<?php
declare(strict_types=1);
namespace App\Tesouraria\UI\Http\Controller;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use App\Tesouraria\Application\DTO\CreateConciliacaoRequest;
use App\Tesouraria\Application\DTO\CreateConciliacaoRegraRequest;
use App\Tesouraria\Application\DTO\TesourariaResponses;
use App\Tesouraria\Application\Service\ConciliacaoRegraService;
use App\Tesouraria\Application\Service\ConciliacaoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
final class ConciliacaoController extends AbstractController
{
    public function __construct(private readonly ConciliacaoService $conciliacaoService, private readonly ConciliacaoRegraService $regraService, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/api/v1/conciliacoes', methods:['POST'])]
    public function create(Request $request): JsonResponse { $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateConciliacaoRequest(); $dto->companyId=isset($p['companyId'])?(int)$p['companyId']:null; $dto->extratoBancarioId=isset($p['extratoBancarioId'])?(int)$p['extratoBancarioId']:null; $dto->movimentoFinanceiroId=isset($p['movimentoFinanceiroId'])?(int)$p['movimentoFinanceiroId']:null; $dto->baixaId=isset($p['baixaId'])?(int)$p['baixaId']:null; $dto->modo=(string)($p['modo']??'manual'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('tesouraria.conciliacoes.create',$dto->companyId)); $c=$this->conciliacaoService->create($dto); return $this->responseFactory->success(['item'=>TesourariaResponses::conciliacao($c)],JsonResponse::HTTP_CREATED); }
    #[Route('/api/v1/conciliacoes', methods:['GET'])]
    public function list(Request $request): JsonResponse { $companyId=$request->query->getInt('companyId'); $empresaId=$request->query->has('empresaId')?$request->query->getInt('empresaId'):null; $unidadeId=$request->query->has('unidadeId')?$request->query->getInt('unidadeId'):null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('tesouraria.conciliacoes.view',$companyId,$empresaId,$unidadeId)); $items=$this->conciliacaoService->list($companyId,$empresaId,$unidadeId); return $this->responseFactory->success(['items'=>array_map([TesourariaResponses::class,'conciliacao'],$items)]); }
    #[Route('/api/v1/conciliacao-regras', methods:['POST'])]
    public function createRegra(Request $request): JsonResponse { $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateConciliacaoRegraRequest(); $dto->companyId=isset($p['companyId'])?(int)$p['companyId']:null; $dto->empresaId=isset($p['empresaId'])?(int)$p['empresaId']:null; $dto->unidadeId=isset($p['unidadeId'])?(int)$p['unidadeId']:null; $dto->contaFinanceiraId=isset($p['contaFinanceiraId'])?(int)$p['contaFinanceiraId']:null; $dto->descricaoContains=(string)($p['descricaoContains']??''); $dto->tipoMovimentoSugerido=(string)($p['tipoMovimentoSugerido']??'credito'); $dto->aplicacao=(string)($p['aplicacao']??'sugerir_movimento'); $dto->status=(string)($p['status']??'active'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('tesouraria.conciliacao_regras.create',$dto->companyId,$dto->empresaId,$dto->unidadeId)); $regra=$this->regraService->create($dto); return $this->responseFactory->success(['item'=>TesourariaResponses::regra($regra)],JsonResponse::HTTP_CREATED); }
}
