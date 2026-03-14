<?php

declare(strict_types=1);

namespace App\Contabilidade\Application\DTO;

final class ContaContabilOutput
{
    public function __construct(public array $data = [])
    {
    }
}
