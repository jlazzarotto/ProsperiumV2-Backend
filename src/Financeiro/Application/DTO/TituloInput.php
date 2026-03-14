<?php

declare(strict_types=1);

namespace App\Financeiro\Application\DTO;

final class TituloInput
{
    public function __construct(public array $data = [])
    {
    }
}
