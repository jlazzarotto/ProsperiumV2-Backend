<?php
declare(strict_types=1);
namespace App\Cadastro\UI\Http\Controller;
use App\Cadastro\Application\DTO\CadastroResponses;
use App\Cadastro\Application\DTO\CreateCategoriaFinanceiraRequest;
use App\Cadastro\Application\Service\CategoriaFinanceiraService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/v1/categorias-financeiras')]
final class CategoriaFinanceiraController extends AbstractController { public function __construct(private readonly CategoriaFinanceiraService $service, private readonly JsonResponseFactory $responseFactory) {} #[Route('', methods:['GET'])] public function list(Request $request): JsonResponse { $companyId=$request->query->getInt('companyId'); $status=$request->query->getString('status')?:null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('cadastro.categorias_financeiras.view',$companyId)); return $this->responseFactory->success(['items'=>array_map(static fn($i)=>CadastroResponses::categoria($i),$this->service->list($companyId,$status))]); } #[Route('', methods:['POST'])] public function create(Request $request): JsonResponse { $payload=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateCategoriaFinanceiraRequest(); $dto->companyId=isset($payload['companyId'])?(int)$payload['companyId']:null; $dto->parentId=isset($payload['parentId'])?(int)$payload['parentId']:null; $dto->codigo=(string)($payload['codigo']??''); $dto->nome=(string)($payload['nome']??''); $dto->tipo=(string)($payload['tipo']??'despesa'); $dto->status=(string)($payload['status']??'active'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('cadastro.categorias_financeiras.create',$dto->companyId)); return $this->responseFactory->success(['item'=>CadastroResponses::categoria($this->service->create($dto))],JsonResponse::HTTP_CREATED); } }
