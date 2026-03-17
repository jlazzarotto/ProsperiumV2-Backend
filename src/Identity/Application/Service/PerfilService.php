<?php

declare(strict_types=1);

namespace App\Identity\Application\Service;

use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Identity\Application\DTO\CreatePerfilRequest;
use App\Identity\Application\DTO\UpdatePerfilRequest;
use App\Identity\Domain\Entity\Tenant\Perfil;
use App\Identity\Domain\Entity\Tenant\PerfilPermissao;
use App\Identity\Domain\Repository\PerfilPermissaoRepositoryInterface;
use App\Identity\Domain\Repository\PerfilRepositoryInterface;
use App\Identity\Domain\Repository\PermissaoRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;

final class PerfilService
{
    public function __construct(
        private readonly PerfilRepositoryInterface $perfilRepository,
        private readonly PerfilPermissaoRepositoryInterface $perfilPermissaoRepository,
        private readonly PermissaoRepositoryInterface $permissaoRepository,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly RequestValidator $requestValidator,
        private readonly TransactionRunnerInterface $transactionRunner,
        private readonly AuditoriaLogger $auditoriaLogger
    ) {
    }

    public function create(CreatePerfilRequest $request): Perfil
    {
        $this->requestValidator->validate($request);

        $company = $this->companyRepository->findById((int) $request->companyId);

        if ($company === null) {
            throw new ResourceNotFoundException('Company não encontrada.');
        }

        if ($this->perfilRepository->findByCodigo($request->codigo, (int) $company->getId()) !== null) {
            throw new ValidationException([
                'codigo' => ['Já existe um perfil com este código na company.'],
            ]);
        }

        $permissoes = $this->permissaoRepository->findByCodigos($request->permissionCodes);

        if (count($permissoes) !== count(array_unique($request->permissionCodes))) {
            throw new ValidationException([
                'permissionCodes' => ['Uma ou mais permissões informadas não existem.'],
            ]);
        }

        return $this->transactionRunner->run(function () use ($request, $company, $permissoes): Perfil {
            $perfil = new Perfil((int)$company->getId(), $request->codigo, $request->nome, $request->tipo, $request->status);
            $this->perfilRepository->save($perfil);

            foreach ($permissoes as $permissao) {
                $this->perfilPermissaoRepository->save(new PerfilPermissao($perfil, (int) $permissao->getId()));
            }

            $this->auditoriaLogger->log(
                (int) $company->getId(),
                'perfil',
                'identity.perfil.created',
                [
                    'perfilId' => $perfil->getId(),
                    'codigo' => $perfil->getCodigo(),
                ]
            );

            return $perfil;
        });
    }

    /**
     * @return list<array{perfil: Perfil, permissionCodes: list<string>}>
     */
    public function list(?int $companyId = null): array
    {
        $items = [];

        foreach ($this->perfilRepository->listAll($companyId) as $perfil) {
            $items[] = [
                'perfil' => $perfil,
                'permissionCodes' => $this->perfilPermissaoRepository->listPermissionCodesByPerfil((int) $perfil->getId()),
            ];
        }

        return $items;
    }

    public function update(int $id, UpdatePerfilRequest $request): Perfil
    {
        $this->requestValidator->validate($request);

        $perfil = $this->perfilRepository->findById($id);

        if ($perfil === null) {
            throw new ResourceNotFoundException('Perfil não encontrado.');
        }

        // company is now stored as companyId in Perfil
        $companyId = $company?->getId();
        $permissoes = $this->permissaoRepository->findByCodigos($request->permissionCodes);

        if (count($permissoes) !== count(array_unique($request->permissionCodes))) {
            throw new ValidationException([
                'permissionCodes' => ['Uma ou mais permissões informadas não existem.'],
            ]);
        }

        return $this->transactionRunner->run(function () use ($perfil, $request, $permissoes, $companyId): Perfil {
            $perfil->update($request->nome, $request->tipo, $request->status);
            $this->perfilRepository->save($perfil);
            $this->perfilPermissaoRepository->deleteByPerfilId((int) $perfil->getId());

            foreach ($permissoes as $permissao) {
                $this->perfilPermissaoRepository->save(new PerfilPermissao($perfil, (int) $permissao->getId()));
            }

            $this->auditoriaLogger->log(
                $companyId !== null ? (int) $companyId : null,
                'perfil',
                'identity.perfil.updated',
                [
                    'perfilId' => $perfil->getId(),
                    'codigo' => $perfil->getCodigo(),
                ]
            );

            return $perfil;
        });
    }
}
