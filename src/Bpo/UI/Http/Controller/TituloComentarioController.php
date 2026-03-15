<?php
declare(strict_types=1);
namespace App\Bpo\UI\Http\Controller;
use App\Bpo\Application\DTO\BpoResponses;
use App\Bpo\Application\DTO\CreateComentarioTituloRequest;
use App\Bpo\Application\Service\ComentarioTituloService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
final class TituloComentarioController extends AbstractController
{
    public function __construct(private readonly ComentarioTituloService $service, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/api/v1/titulos/{id}/comentarios', methods: ['POST'])] public function create(int $id, Request $request): JsonResponse { $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new CreateComentarioTituloRequest(); $dto->companyId=isset($p['companyId'])?(int)$p['companyId']:null; $dto->empresaId=isset($p['empresaId'])?(int)$p['empresaId']:null; $dto->unidadeId=isset($p['unidadeId'])?(int)$p['unidadeId']:null; $dto->comentario=(string)($p['comentario']??''); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('bpo.titulos.comentarios.create',$dto->companyId,$dto->empresaId,$dto->unidadeId)); $comentario=$this->service->create($id,$dto); return $this->responseFactory->success(['item'=>BpoResponses::comentario($comentario)],JsonResponse::HTTP_CREATED); }
}
