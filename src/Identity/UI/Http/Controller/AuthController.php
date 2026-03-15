<?php

declare(strict_types=1);

namespace App\Identity\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController
{
    #[Route('/api/v1/auth/login', name: 'api_v1_auth_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'error' => [
                'message' => 'Firewall JWT deveria interceptar este endpoint antes do controller.',
            ],
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
