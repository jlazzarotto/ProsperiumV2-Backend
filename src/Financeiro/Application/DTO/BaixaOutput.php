<?php

declare(strict_types=1);

namespace App\Financeiro\Application\DTO;

final class BaixaOutput
{
    public function __construct(public array $data = [])
    {
    }
}
