<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Repository;

use App\Cadastro\Domain\Entity\Referencia\Uf;

interface UfRepositoryInterface
{
    public function findByCodigoIbge(int $codigoIbge): ?Uf;

    /** @return list<Uf> */
    public function listAll(): array;

    /** @return list<Uf> */
    public function listFiltered(?string $query = null, ?string $sigla = null, ?string $status = 'active', int $limit = 100): array;
}
