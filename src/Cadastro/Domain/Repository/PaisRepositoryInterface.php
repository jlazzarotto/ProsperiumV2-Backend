<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Repository;

use App\Cadastro\Domain\Entity\Pais;

interface PaisRepositoryInterface
{
    public function findByCodigoM49(int $codigoM49): ?Pais;

    /** @return list<Pais> */
    public function listAll(): array;

    /** @return list<Pais> */
    public function listFiltered(?string $query = null, ?string $status = 'active', int $limit = 100): array;
}
