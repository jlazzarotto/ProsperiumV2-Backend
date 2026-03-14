<?php

declare(strict_types=1);

namespace App\Conciliacao\Application\DTO;

final class ImportarExtratoInput
{
    public function __construct(public array $data = [])
    {
    }
}
