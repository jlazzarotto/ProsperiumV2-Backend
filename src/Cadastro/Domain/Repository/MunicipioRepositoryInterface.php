<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Repository;

use App\Cadastro\Domain\Entity\Referencia\Municipio;

interface MunicipioRepositoryInterface
{
    public function save(Municipio $municipio): void;

    public function findByCodigoIbge(int $codigoIbge): ?Municipio;

    /**
     * @return list<Municipio>
     */
    public function listFiltered(?string $ufSigla = null, ?string $query = null, ?int $codigoIbge = null, ?string $status = 'active', int $limit = 100): array;

    /** @return list<Municipio> */
    public function listAll(): array;
}
