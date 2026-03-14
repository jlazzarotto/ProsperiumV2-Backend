<?php

declare(strict_types=1);

namespace App\Contabilidade\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class LancamentoContabilController
{
    #[Route(path: '/health', name: 'lancamentocontabilcontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'LancamentoContabilController']);
    }
}
