<?php

declare(strict_types=1);

namespace App\Cadastro\Application\Service;

use App\Cadastro\Domain\Repository\UfRepositoryInterface;

final class UfService
{
    public function __construct(private readonly UfRepositoryInterface $repo)
    {
    }

    public function list(?string $query = null, ?string $sigla = null, ?string $status = 'active', int $limit = 100): array
    {
        return $this->repo->listFiltered($query, $sigla, $status, $limit);
    }
}
