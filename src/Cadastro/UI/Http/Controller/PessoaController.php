<?php
declare(strict_types=1);
namespace App\Cadastro\UI\Http\Controller;
use App\Cadastro\Application\DTO\CadastroResponses;
use App\Cadastro\Application\DTO\CreatePessoaRequest;
use App\Cadastro\Application\Service\PessoaService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/v1/pessoas')]
final class PessoaController extends AbstractController { public function __construct(private readonly PessoaService $service, private readonly JsonResponseFactory $responseFactory) {} #[Route('', methods:['GET'])] public function list(Request $request): JsonResponse { $companyId=$request->query->getInt('companyId'); $empresaId=$request->query->has('empresaId')?$request->query->getInt('empresaId'):null; $status=$request->query->getString('status')?:null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('cadastros.pessoas.view',$companyId,$empresaId)); return $this->responseFactory->success(['items'=>array_map(static fn($p)=>CadastroResponses::pessoa($p),$this->service->list($companyId,$empresaId,$status))]); } #[Route('', methods:['POST'])] public function create(Request $request): JsonResponse { $payload=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreatePessoaRequest(); $dto->companyId=isset($payload['companyId'])?(int)$payload['companyId']:null; $dto->empresaId=isset($payload['empresaId'])?(int)$payload['empresaId']:null; $dto->nome=(string)($payload['nome']??''); $dto->documento=isset($payload['documento'])?(string)$payload['documento']:null; $dto->classificacao=(string)($payload['classificacao']??'ambos'); $dto->status=(string)($payload['status']??'active'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('cadastros.pessoas.create_edit',$dto->companyId,$dto->empresaId)); return $this->responseFactory->success(['item'=>CadastroResponses::pessoa($this->service->create($dto))],JsonResponse::HTTP_CREATED); } }
