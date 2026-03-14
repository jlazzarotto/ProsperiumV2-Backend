<?php

declare(strict_types=1);

namespace App\Financeiro\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class BaixaController
{
    #[Route(path: '/health', name: 'baixacontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'BaixaController']);
    }
}
