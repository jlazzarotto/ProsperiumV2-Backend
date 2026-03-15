<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\Perfil;

interface PerfilRepositoryInterface
{
    public function save(Perfil $perfil): void;

    public function findById(int $id): ?Perfil;

    public function findByCodigo(string $codigo, ?int $companyId = null): ?Perfil;

    /**
     * @param list<string> $codigos
     * @return list<Perfil>
     */
    public function findByCodigos(array $codigos, ?int $companyId = null): array;

    /**
     * @return list<Perfil>
     */
    public function listAll(?int $companyId = null): array;
}
