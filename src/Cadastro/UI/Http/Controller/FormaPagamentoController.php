<?php
declare(strict_types=1);
namespace App\Cadastro\UI\Http\Controller;
use App\Cadastro\Application\DTO\CadastroResponses;
use App\Cadastro\Application\DTO\CreateFormaPagamentoRequest;
use App\Cadastro\Application\Service\FormaPagamentoService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/v1/formas-pagamento')]
final class FormaPagamentoController extends AbstractController { public function __construct(private readonly FormaPagamentoService $service, private readonly JsonResponseFactory $responseFactory) {} #[Route('', methods:['GET'])] public function list(Request $request): JsonResponse { $companyId=$request->query->getInt('companyId'); $status=$request->query->getString('status')?:null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('cadastros.formas_pagamento.view',$companyId)); return $this->responseFactory->success(['items'=>array_map(static fn($i)=>CadastroResponses::forma($i),$this->service->list($companyId,$status))]); } #[Route('', methods:['POST'])] public function create(Request $request): JsonResponse { $payload=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateFormaPagamentoRequest(); $dto->companyId=isset($payload['companyId'])?(int)$payload['companyId']:null; $dto->codigo=(string)($payload['codigo']??''); $dto->nome=(string)($payload['nome']??''); $dto->tipo=(string)($payload['tipo']??'manual'); $dto->status=(string)($payload['status']??'active'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('cadastros.formas_pagamento.create_edit',$dto->companyId)); return $this->responseFactory->success(['item'=>CadastroResponses::forma($this->service->create($dto))],JsonResponse::HTTP_CREATED); } }
