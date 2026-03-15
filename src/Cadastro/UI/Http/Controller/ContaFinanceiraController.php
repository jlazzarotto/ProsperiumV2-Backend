<?php
declare(strict_types=1);
namespace App\Cadastro\UI\Http\Controller;
use App\Cadastro\Application\DTO\CadastroResponses;
use App\Cadastro\Application\DTO\CreateContaFinanceiraRequest;
use App\Cadastro\Application\Service\ContaFinanceiraService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/v1/contas-financeiras')]
final class ContaFinanceiraController extends AbstractController { public function __construct(private readonly ContaFinanceiraService $service, private readonly JsonResponseFactory $responseFactory) {} #[Route('', methods:['GET'])] public function list(Request $request): JsonResponse { $companyId=$request->query->getInt('companyId'); $empresaId=$request->query->has('empresaId')?$request->query->getInt('empresaId'):null; $status=$request->query->getString('status')?:null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('cadastro.contas_financeiras.view',$companyId,$empresaId)); return $this->responseFactory->success(['items'=>array_map(static fn($i)=>CadastroResponses::conta($i),$this->service->list($companyId,$empresaId,$status))]); } #[Route('', methods:['POST'])] public function create(Request $request): JsonResponse { $payload=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateContaFinanceiraRequest(); $dto->companyId=isset($payload['companyId'])?(int)$payload['companyId']:null; $dto->empresaId=isset($payload['empresaId'])?(int)$payload['empresaId']:null; $dto->unidadeId=isset($payload['unidadeId'])?(int)$payload['unidadeId']:null; $dto->codigo=(string)($payload['codigo']??''); $dto->nome=(string)($payload['nome']??''); $dto->tipo=(string)($payload['tipo']??'caixa'); $dto->status=(string)($payload['status']??'active'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('cadastro.contas_financeiras.create',$dto->companyId,$dto->empresaId,$dto->unidadeId)); return $this->responseFactory->success(['item'=>CadastroResponses::conta($this->service->create($dto))],JsonResponse::HTTP_CREATED); } }
