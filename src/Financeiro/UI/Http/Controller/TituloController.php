<?php

declare(strict_types=1);

namespace App\Financeiro\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TituloController
{
    #[Route(path: '/health', name: 'titulocontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'TituloController']);
    }
}
