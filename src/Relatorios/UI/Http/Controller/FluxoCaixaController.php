<?php

declare(strict_types=1);

namespace App\Relatorios\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class FluxoCaixaController
{
    #[Route(path: '/health', name: 'fluxocaixacontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'FluxoCaixaController']);
    }
}
