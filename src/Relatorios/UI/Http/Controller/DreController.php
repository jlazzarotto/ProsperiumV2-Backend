<?php

declare(strict_types=1);

namespace App\Relatorios\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class DreController
{
    #[Route(path: '/health', name: 'drecontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'DreController']);
    }
}
