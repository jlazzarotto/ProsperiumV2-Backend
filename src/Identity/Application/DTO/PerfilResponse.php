<?php

declare(strict_types=1);

namespace App\Identity\Application\DTO;

use App\Identity\Domain\Entity\Perfil;

final class PerfilResponse
{
    /**
     * @param list<string> $permissionCodes
     */
    public static function fromEntity(Perfil $perfil, array $permissionCodes): array
    {
        return [
            'id' => $perfil->getId(),
            'companyId' => $perfil->getCompany()?->getId(),
            'codigo' => $perfil->getCodigo(),
            'nome' => $perfil->getNome(),
            'tipo' => $perfil->getTipo(),
            'status' => $perfil->getStatus(),
            'permissionCodes' => $permissionCodes,
        ];
    }
}
