<?php

declare(strict_types=1);

namespace App\Cadastro\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class CategoriaFinanceiraController
{
    #[Route(path: '/health', name: 'categoriafinanceiracontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'CategoriaFinanceiraController']);
    }
}
