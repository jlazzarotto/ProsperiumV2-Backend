<?php

declare(strict_types=1);

namespace App\Relatorios\Application\DTO;

final class FluxoCaixaOutput
{
    public function __construct(public array $data = [])
    {
    }
}
