<?php

declare(strict_types=1);

namespace App\Auditoria\Application\DTO;

final class LogAuditoriaOutput
{
    public function __construct(public array $data = [])
    {
    }
}
