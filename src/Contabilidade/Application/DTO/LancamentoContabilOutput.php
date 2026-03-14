<?php

declare(strict_types=1);

namespace App\Contabilidade\Application\DTO;

final class LancamentoContabilOutput
{
    public function __construct(public array $data = [])
    {
    }
}
