<?php
declare(strict_types=1);
namespace App\Contabil\UI\Http\Controller;
use App\Contabil\Application\DTO\ContabilResponses;
use App\Contabil\Application\DTO\CreateLancamentoContabilRequest;
use App\Contabil\Application\Service\LancamentoContabilService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
final class LancamentoContabilController extends AbstractController
{
    public function __construct(private readonly LancamentoContabilService $service, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/api/v1/lancamentos-contabeis', methods: ['POST'])] public function create(Request $request): JsonResponse { $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateLancamentoContabilRequest(); $dto->companyId=isset($p['companyId'])?(int)$p['companyId']:null; $dto->empresaId=isset($p['empresaId'])?(int)$p['empresaId']:null; $dto->unidadeId=isset($p['unidadeId'])?(int)$p['unidadeId']:null; $dto->tituloId=isset($p['tituloId'])?(int)$p['tituloId']:null; $dto->dataLancamento=(string)($p['dataLancamento']??''); $dto->historico=(string)($p['historico']??''); $dto->itens=is_array($p['itens']??null)?$p['itens']:[]; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('contabil.lancamentos_contabeis.create',$dto->companyId,$dto->empresaId,$dto->unidadeId)); $result=$this->service->create($dto); return $this->responseFactory->success(['item'=>ContabilResponses::lancamento($result['lancamento'],$result['itens'])],JsonResponse::HTTP_CREATED); }
}
