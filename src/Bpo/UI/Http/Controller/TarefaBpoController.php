<?php
declare(strict_types=1);
namespace App\Bpo\UI\Http\Controller;
use App\Bpo\Application\DTO\BpoResponses;
use App\Bpo\Application\DTO\CreateTarefaBpoRequest;
use App\Bpo\Application\Service\TarefaOperacionalBpoService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
final class TarefaBpoController extends AbstractController
{
    public function __construct(private readonly TarefaOperacionalBpoService $service, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/api/v1/tarefas-bpo', methods: ['POST'])] public function create(Request $request): JsonResponse { $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateTarefaBpoRequest(); $dto->companyId=isset($p['companyId'])?(int)$p['companyId']:null; $dto->empresaId=isset($p['empresaId'])?(int)$p['empresaId']:null; $dto->unidadeId=isset($p['unidadeId'])?(int)$p['unidadeId']:null; $dto->tituloId=isset($p['tituloId'])?(int)$p['tituloId']:null; $dto->responsavelUserId=isset($p['responsavelUserId'])?(int)$p['responsavelUserId']:null; $dto->tipo=(string)($p['tipo']??''); $dto->descricao=(string)($p['descricao']??''); $dto->prioridade=(string)($p['prioridade']??'media'); $dto->prazoEm=isset($p['prazoEm'])?(string)$p['prazoEm']:null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('bpo.tarefas.create',$dto->companyId,$dto->empresaId,$dto->unidadeId)); $tarefa=$this->service->create($dto); return $this->responseFactory->success(['item'=>BpoResponses::tarefa($tarefa)],JsonResponse::HTTP_CREATED); }
    #[Route('/api/v1/tarefas-bpo', methods: ['GET'])] public function list(Request $request): JsonResponse { $companyId=$request->query->getInt('companyId'); $empresaId=$request->query->has('empresaId')?$request->query->getInt('empresaId'):null; $unidadeId=$request->query->has('unidadeId')?$request->query->getInt('unidadeId'):null; $status=$request->query->get('status'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('bpo.tarefas.view',$companyId,$empresaId,$unidadeId)); $items=$this->service->list($companyId,$empresaId,$unidadeId,is_string($status)?$status:null); return $this->responseFactory->success(['items'=>array_map([BpoResponses::class,'tarefa'],$items)]); }
}
