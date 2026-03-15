<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Repository;

use App\Cadastro\Domain\Entity\Banco;

interface BancoRepositoryInterface
{
    public function findById(int $id): ?Banco;

    /** @return list<Banco> */
    public function listAll(?string $status = null): array;
}
