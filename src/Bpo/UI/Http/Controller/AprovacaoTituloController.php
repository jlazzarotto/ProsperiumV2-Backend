<?php
declare(strict_types=1);
namespace App\Bpo\UI\Http\Controller;
use App\Bpo\Application\DTO\BpoResponses;
use App\Bpo\Application\DTO\CreateAprovacaoTituloRequest;
use App\Bpo\Application\Service\AprovacaoTituloService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
final class AprovacaoTituloController extends AbstractController
{
    public function __construct(private readonly AprovacaoTituloService $service, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/api/v1/aprovacoes-titulos', methods: ['POST'])] public function create(Request $request): JsonResponse { $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateAprovacaoTituloRequest(); $dto->companyId=isset($p['companyId'])?(int)$p['companyId']:null; $dto->empresaId=isset($p['empresaId'])?(int)$p['empresaId']:null; $dto->unidadeId=isset($p['unidadeId'])?(int)$p['unidadeId']:null; $dto->tituloId=isset($p['tituloId'])?(int)$p['tituloId']:null; $dto->aprovadorIds=array_map('intval',$p['aprovadorIds']??[]); $dto->tipoOperacao=isset($p['tipoOperacao'])?(string)$p['tipoOperacao']:null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('bpo.aprovacoes.create',$dto->companyId,$dto->empresaId,$dto->unidadeId)); $result=$this->service->create($dto); return $this->responseFactory->success(['item'=>BpoResponses::aprovacao($result['aprovacao'],$result['itens'])],JsonResponse::HTTP_CREATED); }
}
