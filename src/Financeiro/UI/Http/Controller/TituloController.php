<?php
declare(strict_types=1);
namespace App\Financeiro\UI\Http\Controller;
use App\Financeiro\Application\DTO\CreateTituloRequest;
use App\Financeiro\Application\DTO\FinanceiroResponses;
use App\Financeiro\Application\DTO\ParcelarTituloRequest;
use App\Financeiro\Application\Service\TituloService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/v1/titulos')]
final class TituloController extends AbstractController
{
    public function __construct(private readonly TituloService $service, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('', methods:['POST'])]
    public function create(Request $request): JsonResponse { $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateTituloRequest(); $dto->companyId=isset($p['companyId'])?(int)$p['companyId']:null; $dto->empresaId=isset($p['empresaId'])?(int)$p['empresaId']:null; $dto->unidadeId=isset($p['unidadeId'])?(int)$p['unidadeId']:null; $dto->pessoaId=isset($p['pessoaId'])?(int)$p['pessoaId']:null; $dto->contaFinanceiraId=isset($p['contaFinanceiraId'])?(int)$p['contaFinanceiraId']:null; $dto->tipo=(string)($p['tipo']??'pagar'); $dto->numeroDocumento=isset($p['numeroDocumento'])?(string)$p['numeroDocumento']:null; $dto->valorTotal=(string)($p['valorTotal']??'0.00'); $dto->dataEmissao=(string)($p['dataEmissao']??''); $dto->observacoes=isset($p['observacoes'])?(string)$p['observacoes']:null; $dto->primeiroVencimento=isset($p['primeiroVencimento'])?(string)$p['primeiroVencimento']:null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('financeiro.titulos.create',$dto->companyId,$dto->empresaId,$dto->unidadeId)); $result=$this->service->getById((int)$this->service->create($dto)->getId()); return $this->responseFactory->success(['item'=>FinanceiroResponses::titulo($result['titulo'],$result['parcelas'],$result['anexos'])],JsonResponse::HTTP_CREATED); }
    #[Route('', methods:['GET'])]
    public function list(Request $request): JsonResponse { $companyId=$request->query->getInt('companyId'); $empresaId=$request->query->has('empresaId')?$request->query->getInt('empresaId'):null; $unidadeId=$request->query->has('unidadeId')?$request->query->getInt('unidadeId'):null; $tipo=$request->query->getString('tipo')?:null; $status=$request->query->getString('status')?:null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('financeiro.titulos.view',$companyId,$empresaId,$unidadeId)); $items=array_map(fn($t)=>FinanceiroResponses::titulo($t,$this->service->getById((int)$t->getId())['parcelas']),$this->service->list($companyId,$empresaId,$unidadeId,$tipo,$status)); return $this->responseFactory->success(['items'=>$items]); }
    #[Route('/{id}', methods:['GET'])]
    public function getById(int $id): JsonResponse { $result=$this->service->getById($id); $titulo=$result['titulo']; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('financeiro.titulos.view',(int)$titulo->getCompany()->getId(),(int)$titulo->getEmpresa()->getId(),(int)$titulo->getUnidade()->getId())); return $this->responseFactory->success(['item'=>FinanceiroResponses::titulo($titulo,$result['parcelas'],$result['anexos'])]); }
    #[Route('/{id}/parcelar', methods:['POST'])]
    public function parcelar(int $id, Request $request): JsonResponse { $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new ParcelarTituloRequest(); $dto->parcelas=is_array($p['parcelas']??null)?$p['parcelas']:[]; $result=$this->service->getById($id); $titulo=$result['titulo']; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('financeiro.titulos.parcelar',(int)$titulo->getCompany()->getId(),(int)$titulo->getEmpresa()->getId(),(int)$titulo->getUnidade()->getId())); $parcelas=$this->service->parcelar($id,$dto); return $this->responseFactory->success(['items'=>array_map([FinanceiroResponses::class,'parcela'],$parcelas)]); }
}
