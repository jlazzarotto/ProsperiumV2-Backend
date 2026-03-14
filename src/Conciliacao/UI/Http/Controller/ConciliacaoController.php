<?php

declare(strict_types=1);

namespace App\Conciliacao\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ConciliacaoController
{
    #[Route(path: '/health', name: 'conciliacaocontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'ConciliacaoController']);
    }
}
