<?php

declare(strict_types=1);

namespace App\Cadastro\UI\Http\Controller;

use App\Cadastro\Application\DTO\CadastroResponses;
use App\Cadastro\Application\Service\MunicipioService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/municipios')]
final class MunicipioController extends AbstractController
{
    public function __construct(
        private readonly MunicipioService $service,
        private readonly JsonResponseFactory $responseFactory,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('cadastros.pessoas.view'),
        );

        $ufSigla = $request->query->getString('ufSigla') ?: null;
        $query = $request->query->getString('q') ?: null;
        $codigoIbge = $request->query->has('codigoIbge') ? $request->query->getInt('codigoIbge') : null;
        $status = $request->query->getString('status') ?: 'active';
        $limit = $request->query->has('limit') ? $request->query->getInt('limit') : 100;

        $items = $this->service->list($ufSigla, $query, $codigoIbge, $status, $limit);

        return $this->responseFactory->success([
            'items' => array_map([CadastroResponses::class, 'municipio'], $items),
        ]);
    }
}
