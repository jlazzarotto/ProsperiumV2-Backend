<?php
declare(strict_types=1);
namespace App\Contabil\UI\Http\Controller;
use App\Contabil\Application\DTO\ContabilResponses;
use App\Contabil\Application\DTO\CreateContaContabilRequest;
use App\Contabil\Application\Service\ContaContabilService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
final class ContaContabilController extends AbstractController
{
    public function __construct(private readonly ContaContabilService $service, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/api/v1/contas-contabeis', methods: ['GET'])] public function list(Request $request): JsonResponse { $companyId=$request->query->getInt('companyId'); $tipo=$request->query->get('tipo'); $status=$request->query->get('status'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('contabil.contas_contabeis.view',$companyId)); $items=$this->service->list($companyId,is_string($tipo)?$tipo:null,is_string($status)?$status:null); return $this->responseFactory->success(['items'=>array_map([ContabilResponses::class,'conta'],$items)]); }
    #[Route('/api/v1/contas-contabeis', methods: ['POST'])] public function create(Request $request): JsonResponse { $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateContaContabilRequest(); $dto->companyId=isset($p['companyId'])?(int)$p['companyId']:null; $dto->parentId=isset($p['parentId'])?(int)$p['parentId']:null; $dto->codigo=(string)($p['codigo']??''); $dto->nome=(string)($p['nome']??''); $dto->tipo=(string)($p['tipo']??'ativo'); $dto->status=(string)($p['status']??'active'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('contabil.contas_contabeis.create',$dto->companyId)); $conta=$this->service->create($dto); return $this->responseFactory->success(['item'=>ContabilResponses::conta($conta)],JsonResponse::HTTP_CREATED); }
}
