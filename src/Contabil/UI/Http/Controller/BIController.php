<?php
declare(strict_types=1);
namespace App\Contabil\UI\Http\Controller;
use App\Contabil\Application\DTO\ContabilResponses;
use App\Contabil\Application\Service\BIService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
final class BIController extends AbstractController
{
    public function __construct(private readonly BIService $service, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/api/v1/dre', methods: ['GET'])] public function dre(Request $request): JsonResponse { $companyId=$request->query->getInt('companyId'); $empresaId=$request->query->getInt('empresaId'); $unidadeId=$request->query->getInt('unidadeId'); $data=$request->query->get('dataReferencia'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('contabil.dre.view',$companyId,$empresaId,$unidadeId)); $result=$this->service->dre($companyId,$empresaId,$unidadeId,new \DateTimeImmutable(is_string($data)&&$data!==''?$data:'today')); return $this->responseFactory->success($result); }
    #[Route('/api/v1/indicadores-financeiros', methods: ['GET'])] public function indicadores(Request $request): JsonResponse { $companyId=$request->query->getInt('companyId'); $empresaId=$request->query->getInt('empresaId'); $unidadeId=$request->query->getInt('unidadeId'); $data=$request->query->get('dataReferencia'); $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('contabil.indicadores.view',$companyId,$empresaId,$unidadeId)); $items=$this->service->indicadores($companyId,$empresaId,$unidadeId,new \DateTimeImmutable(is_string($data)&&$data!==''?$data:'today')); return $this->responseFactory->success(['items'=>array_map([ContabilResponses::class,'indicador'],$items)]); }
}
