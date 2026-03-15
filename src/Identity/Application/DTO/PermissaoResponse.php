<?php

declare(strict_types=1);

namespace App\Identity\Application\DTO;

use App\Identity\Domain\Entity\Permissao;

final class PermissaoResponse
{
    public static function fromEntity(Permissao $permissao): array
    {
        return [
            'id' => $permissao->getId(),
            'modulo' => $permissao->getModulo()->getCodigo(),
            'codigo' => $permissao->getCodigo(),
            'nome' => $permissao->getNome(),
            'status' => $permissao->getStatus(),
        ];
    }
}
