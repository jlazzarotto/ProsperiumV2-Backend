<?php

declare(strict_types=1);

namespace App\Cadastro\UI\Http\Controller;

use App\Cadastro\Application\DTO\CadastroResponses;
use App\Cadastro\Application\Service\BancoService;
use App\Identity\Application\Security\PermissionContext;
use App\Identity\Infrastructure\Security\Voter\PermissionVoter;
use App\Shared\Infrastructure\Http\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/bancos')]
final class BancoController extends AbstractController
{
    public function __construct(
        private readonly BancoService $service,
        private readonly JsonResponseFactory $responseFactory,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $companyId = $request->query->getInt('companyId');
        $status = $request->query->getString('status') ?: 'active';

        $this->denyAccessUnlessGranted(
            PermissionVoter::ATTRIBUTE,
            new PermissionContext('cadastros.contas_financeiras.view', $companyId),
        );

        return $this->responseFactory->success([
            'items' => array_map(
                static fn ($banco) => CadastroResponses::banco($banco),
                $this->service->list($status),
            ),
        ]);
    }
}
