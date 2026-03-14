<?php

declare(strict_types=1);

namespace App\Identity\Application\DTO;

final class UsuarioOutput
{
    public function __construct(public array $data = [])
    {
    }
}
