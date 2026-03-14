<?php

declare(strict_types=1);

namespace App\Cadastro\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class FormaPagamentoController
{
    #[Route(path: '/health', name: 'formapagamentocontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'FormaPagamentoController']);
    }
}
