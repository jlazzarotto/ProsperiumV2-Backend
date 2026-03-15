<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\Permissao;

interface PermissaoRepositoryInterface
{
    public function findByCodigo(string $codigo): ?Permissao;

    /**
     * @param list<string> $codigos
     * @return list<Permissao>
     */
    public function findByCodigos(array $codigos): array;

    /**
     * @return list<Permissao>
     */
    public function listAll(?string $moduloCodigo = null): array;
}
