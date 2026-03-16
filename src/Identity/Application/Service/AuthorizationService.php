<?php

declare(strict_types=1);

namespace App\Identity\Application\Service;

use App\Identity\Application\Security\PermissionContext;
use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Identity\Domain\Repository\UserEmpresaRepositoryInterface;
use App\Identity\Domain\Repository\UserPerfilRepositoryInterface;
use App\Identity\Domain\Repository\UserUnidadeRepositoryInterface;

final class AuthorizationService
{
    public function __construct(
        private readonly UserCompanyRepositoryInterface $userCompanyRepository,
        private readonly UserEmpresaRepositoryInterface $userEmpresaRepository,
        private readonly UserUnidadeRepositoryInterface $userUnidadeRepository,
        private readonly UserPerfilRepositoryInterface $userPerfilRepository
    ) {
    }

    public function can(User $user, PermissionContext $context): bool
    {
        $userId = $user->getId();

        if ($userId === null || !$user->isAtivo()) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }

        if (!$user->isAdmin()) {
            return false;
        }

        if ($context->companyId !== null && !$this->userCompanyRepository->userHasCompany($userId, $context->companyId)) {
            return false;
        }

        if ($context->companyId !== null && $context->empresaId !== null && !$this->userEmpresaRepository->userHasEmpresa($userId, $context->companyId, $context->empresaId)) {
            return false;
        }

        if ($context->companyId !== null && $context->unidadeId !== null && !$this->userUnidadeRepository->userHasUnidade($userId, $context->companyId, $context->unidadeId)) {
            return false;
        }

        return $this->userPerfilRepository->userHasPermission(
            $userId,
            $context->permissionCode,
            $context->companyId,
            $context->empresaId,
            $context->unidadeId
        );
    }
}
