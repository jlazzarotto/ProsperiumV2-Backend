<?php
declare(strict_types=1);
namespace App\Tesouraria\UI\Http\Controller;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use App\Tesouraria\Application\DTO\ImportarExtratoRequest;
use App\Tesouraria\Application\DTO\TesourariaResponses;
use App\Tesouraria\Application\Service\ExtratoBancarioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/v1/extratos-bancarios')]
final class ExtratoBancarioController extends AbstractController
{
    public function __construct(private readonly ExtratoBancarioService $service, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/importar', methods:['POST'])]
    public function importar(Request $request): JsonResponse { $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new ImportarExtratoRequest(); $dto->companyId=isset($p['companyId'])?(int)$p['companyId']:null; $dto->empresaId=isset($p['empresaId'])?(int)$p['empresaId']:null; $dto->unidadeId=isset($p['unidadeId'])?(int)$p['unidadeId']:null; $dto->contaFinanceiraId=isset($p['contaFinanceiraId'])?(int)$p['contaFinanceiraId']:null; $dto->itens=is_array($p['itens']??null)?$p['itens']:[]; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('tesouraria.extratos.importar',$dto->companyId,$dto->empresaId,$dto->unidadeId)); $items=$this->service->importar($dto); return $this->responseFactory->success(['items'=>array_map(fn($e)=>TesourariaResponses::extrato($e,$this->service->sugerir($e)),$items)],JsonResponse::HTTP_CREATED); }
    #[Route('', methods:['GET'])]
    public function list(Request $request): JsonResponse { $companyId=$request->query->getInt('companyId'); $empresaId=$request->query->has('empresaId')?$request->query->getInt('empresaId'):null; $unidadeId=$request->query->has('unidadeId')?$request->query->getInt('unidadeId'):null; $contaFinanceiraId=$request->query->has('contaFinanceiraId')?$request->query->getInt('contaFinanceiraId'):null; $status=$request->query->getString('status')?:null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('tesouraria.extratos.view',$companyId,$empresaId,$unidadeId)); $items=$this->service->list($companyId,$empresaId,$unidadeId,$contaFinanceiraId,$status); return $this->responseFactory->success(['items'=>array_map(fn($e)=>TesourariaResponses::extrato($e,$this->service->sugerir($e)),$items)]); }
}
