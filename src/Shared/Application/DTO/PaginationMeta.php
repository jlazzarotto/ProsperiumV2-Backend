<?php

declare(strict_types=1);

namespace App\Shared\Application\DTO;

final class PaginationMeta
{
    public function __construct(public array $data = [])
    {
    }
}
