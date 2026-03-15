<?php

declare(strict_types=1);

namespace App\Cadastro\Application\Service;

use App\Cadastro\Domain\Repository\MunicipioRepositoryInterface;

final class MunicipioService
{
    public function __construct(private readonly MunicipioRepositoryInterface $repo)
    {
    }

    public function list(?string $ufSigla = null, ?string $query = null, ?int $codigoIbge = null, ?string $status = 'active', int $limit = 100): array
    {
        return $this->repo->listFiltered($ufSigla, $query, $codigoIbge, $status, $limit);
    }
}
