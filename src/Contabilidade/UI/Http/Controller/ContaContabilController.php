<?php

declare(strict_types=1);

namespace App\Contabilidade\UI\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ContaContabilController
{
    #[Route(path: '/health', name: 'contacontabilcontroller', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['resource' => 'ContaContabilController']);
    }
}
