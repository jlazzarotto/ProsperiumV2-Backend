<?php

declare(strict_types=1);

namespace App\Cadastro\Application\DTO;

final class ContaFinanceiraInput
{
    public function __construct(public array $data = [])
    {
    }
}
