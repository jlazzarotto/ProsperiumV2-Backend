<?php

declare(strict_types=1);

namespace App\Identity\Application\DTO;

use App\Identity\Domain\Entity\User;

final class UserResponse
{
    /**
     * @param list<int> $companyIds
     * @param list<int> $empresaIds
     * @param list<int> $unidadeIds
     * @param list<string> $profileCodes
     * @param array<string, bool> $modulosHabilitados
     * @param array<string, array{ver: bool, criar_editar: bool, deletar: bool}> $permissoesModulo
     * @param list<array<string, mixed>> $menu
     */
    public static function fromEntity(
        User $user,
        array $companyIds = [],
        array $empresaIds = [],
        array $unidadeIds = [],
        array $profileCodes = [],
        array $modulosHabilitados = [],
        array $permissoesModulo = [],
        array $menu = []
    ): array {
        return [
            'id' => $user->getId(),
            'companyId' => $user->getCompany()?->getId(),
            'nome' => $user->getNome(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'status' => $user->getStatus(),
            'mfaHabilitado' => $user->isMfaHabilitado(),
            'ultimoLogin' => $user->getUltimoLogin()?->format('Y-m-d H:i:s'),
            'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'companyIds' => $companyIds,
            'empresaIds' => $empresaIds,
            'unidadeIds' => $unidadeIds,
            'profileCodes' => $profileCodes,
            'modulos_habilitados' => $modulosHabilitados,
            'permissoes_modulo' => $permissoesModulo,
            'menu' => $menu,
        ];
    }
}
