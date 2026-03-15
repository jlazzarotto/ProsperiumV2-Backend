<?php
declare(strict_types=1);
namespace App\Financeiro\UI\Http\Controller;
use App\Financeiro\Application\DTO\BaixarParcelaRequest;
use App\Financeiro\Application\DTO\FinanceiroResponses;
use App\Financeiro\Application\Service\BaixaService;
use App\Financeiro\Domain\Repository\TituloParcelaRepositoryInterface;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/v1/parcelas')]
final class ParcelaController extends AbstractController
{
    public function __construct(private readonly BaixaService $service, private readonly TituloParcelaRepositoryInterface $parcelaRepository, private readonly JsonResponseFactory $responseFactory) {}
    #[Route('/{id}/baixar', methods:['POST'])]
    public function baixar(int $id, Request $request): JsonResponse { $parcela=$this->parcelaRepository->findById($id); if($parcela===null){ throw new \App\Shared\Domain\Exception\ResourceNotFoundException('Parcela não encontrada.'); } $p=json_decode($request->getContent(),true,512,JSON_THROW_ON_ERROR); $dto=new BaixarParcelaRequest(); $dto->companyId=isset($p['companyId'])?(int)$p['companyId']:null; $dto->contaFinanceiraId=isset($p['contaFinanceiraId'])?(int)$p['contaFinanceiraId']:null; $dto->valor=(string)($p['valor']??'0.00'); $dto->dataPagamento=(string)($p['dataPagamento']??''); $dto->observacoes=isset($p['observacoes'])?(string)$p['observacoes']:null; $this->denyAccessUnlessGranted(PermissionVoter::ATTRIBUTE,new PermissionContext('financeiro.parcelas.baixar',$dto->companyId,(int)$parcela->getEmpresa()->getId(),(int)$parcela->getUnidade()->getId())); $result=$this->service->baixar($id,$dto); return $this->responseFactory->success(['baixa'=>FinanceiroResponses::baixa($result['baixa']),'parcela'=>FinanceiroResponses::parcela($result['parcela'])]); }
}
