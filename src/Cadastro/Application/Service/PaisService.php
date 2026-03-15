<?php

declare(strict_types=1);

namespace App\Cadastro\Application\Service;

use App\Cadastro\Domain\Repository\PaisRepositoryInterface;

final class PaisService
{
    public function __construct(private readonly PaisRepositoryInterface $repo)
    {
    }

    public function list(?string $query = null, ?string $status = 'active', int $limit = 100): array
    {
        return $this->repo->listFiltered($query, $status, $limit);
    }
}
