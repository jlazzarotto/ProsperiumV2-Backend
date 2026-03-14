<?php

declare(strict_types=1);

namespace App\Auditoria\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class AuditoriaController
{
    #[Route(path: '/health', name: 'auditoriacontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'AuditoriaController']);
    }
}
