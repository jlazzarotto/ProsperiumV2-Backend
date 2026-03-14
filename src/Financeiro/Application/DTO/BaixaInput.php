<?php

declare(strict_types=1);

namespace App\Financeiro\Application\DTO;

final class BaixaInput
{
    public function __construct(public array $data = [])
    {
    }
}
