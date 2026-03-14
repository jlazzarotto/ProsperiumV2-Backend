<?php

declare(strict_types=1);

namespace App\Identity\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController
{
    #[Route(path: '/health', name: 'authcontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'AuthController']);
    }
}
