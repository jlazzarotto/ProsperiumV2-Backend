<?php

declare(strict_types=1);

namespace App\Conciliacao\Application\DTO;

final class ConciliacaoOutput
{
    public function __construct(public array $data = [])
    {
    }
}
