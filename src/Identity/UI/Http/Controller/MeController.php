<?php

declare(strict_types=1);

namespace App\Identity\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class MeController
{
    #[Route(path: '/health', name: 'mecontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'MeController']);
    }
}
