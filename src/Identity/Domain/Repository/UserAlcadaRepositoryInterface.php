<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

interface UserAlcadaRepositoryInterface
{
    public function userHasActiveAlcadaForValue(int $userId, int $companyId, ?int $empresaId, ?int $unidadeId, string $tipoOperacao, string $valor): bool;
}
