<?php

declare(strict_types=1);

namespace App\Company\Application\Service;

use App\Company\Application\DTO\CreateEmpresaRequest;
use App\Company\Application\DTO\UpdateEmpresaRequest;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\TenantInstanceRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;

final class EmpresaService
{
    public function __construct(
        private readonly EmpresaRepositoryInterface $empresaRepository,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly TenantInstanceRepositoryInterface $tenantInstanceRepository,
        private readonly RequestValidator $requestValidator,
        private readonly TransactionRunnerInterface $transactionRunner,
        private readonly AuditoriaLogger $auditoriaLogger,
        private readonly TenantCatalogProjector $tenantCatalogProjector
    ) {
    }

    public function create(CreateEmpresaRequest $request): Empresa
    {
        $this->requestValidator->validate($request);

        $company = $this->companyRepository->findById((int) $request->companyId);

        if ($company === null) {
            throw new ResourceNotFoundException('Company não encontrada.');
        }

        $cnpj = $this->normalizeDocument($request->cnpj);

        if ($cnpj !== null && $this->empresaRepository->existsByCompanyAndCnpj((int) $request->companyId, $cnpj)) {
            throw new ValidationException([
                'cnpj' => ['Já existe uma empresa com este CPF/CNPJ na company informada.'],
            ]);
        }

        $empresa = $this->transactionRunner->run(function () use ($request, $company, $cnpj): Empresa {
            $empresa = new Empresa(
                (int)$company->getId(),
                $request->razaoSocial,
                $request->nomeFantasia,
                $cnpj,
                $request->status,
                $request->apelido,
                $request->abreviatura,
                $request->inscricaoEstadual,
                $request->inscricaoMunicipal,
                $request->cep,
                $request->estado,
                $request->cidade,
                $request->logradouro,
                $request->numero,
                $request->complemento,
                $request->bairro,
            );
            $this->empresaRepository->save($empresa);
            $this->auditoriaLogger->log(
                (int) $company->getId(),
                'empresa',
                'empresa.created',
                [
                    'empresaId' => $empresa->getId(),
                    'cnpj' => $empresa->getCnpj(),
                ]
            );

            return $empresa;
        });

        $databaseKey = $this->tenantInstanceRepository->findByCompanyId((int) $company->getId())?->getDatabaseKey();
        if ($databaseKey === null) {
            throw new ResourceNotFoundException('TenantInstance não encontrada para a company informada.');
        }

        $this->tenantCatalogProjector->syncCompany($company, $databaseKey);
        $this->tenantCatalogProjector->syncEmpresa($empresa);

        return $empresa;
    }

    /**
     * @return list<Empresa>
     */
    public function list(?int $companyId = null, ?string $status = null): array
    {
        return $this->empresaRepository->listAll($companyId, $status);
    }

    public function getById(int $id): Empresa
    {
        $empresa = $this->empresaRepository->findById($id);

        if ($empresa === null) {
            throw new ResourceNotFoundException('Empresa não encontrada.');
        }

        return $empresa;
    }

    public function update(int $id, UpdateEmpresaRequest $request): Empresa
    {
        $this->requestValidator->validate($request);

        $empresa = $this->getById($id);
        $company = $this->companyRepository->findById((int) $request->companyId);

        if ($company === null) {
            throw new ResourceNotFoundException('Company não encontrada.');
        }

        $cnpj = $this->normalizeDocument($request->cnpj);

        if ($cnpj !== null && $this->empresaRepository->existsByCompanyAndCnpj((int) $company->getId(), $cnpj, (int) $empresa->getId())) {
            throw new ValidationException([
                'cnpj' => ['Já existe uma empresa com este CPF/CNPJ na company informada.'],
            ]);
        }

        $empresa = $this->transactionRunner->run(function () use ($empresa, $company, $request, $cnpj): Empresa {
            $empresa->update(
                $company,
                $request->razaoSocial,
                $request->nomeFantasia,
                $cnpj,
                $request->status,
                $request->apelido,
                $request->abreviatura,
                $request->inscricaoEstadual,
                $request->inscricaoMunicipal,
                $request->cep,
                $request->estado,
                $request->cidade,
                $request->logradouro,
                $request->numero,
                $request->complemento,
                $request->bairro,
            );
            $this->empresaRepository->save($empresa);
            $this->auditoriaLogger->log(
                (int) $company->getId(),
                'empresa',
                'empresa.updated',
                [
                    'empresaId' => $empresa->getId(),
                    'cnpj' => $empresa->getCnpj(),
                    'status' => $empresa->getStatus(),
                ]
            );

            return $empresa;
        });

        $databaseKey = $this->tenantInstanceRepository->findByCompanyId((int) $company->getId())?->getDatabaseKey();
        if ($databaseKey === null) {
            throw new ResourceNotFoundException('TenantInstance não encontrada para a company informada.');
        }

        $this->tenantCatalogProjector->syncCompany($company, $databaseKey);
        $this->tenantCatalogProjector->syncEmpresa($empresa);

        return $empresa;
    }

    public function delete(int $id): void
    {
        $empresa = $this->getById($id);
        $company = $this->companyRepository->findById($empresa->getCompanyId());

        $this->transactionRunner->run(function () use ($empresa, $company): void {
            $this->empresaRepository->softDelete($empresa);
            $this->auditoriaLogger->log(
                (int) $company->getId(),
                'empresa',
                'empresa.deleted',
                ['empresaId' => $empresa->getId()]
            );
        });
    }

    private function normalizeDocument(?string $document): ?string
    {
        if ($document === null || $document === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $document) ?? '';

        if ($digits === '') {
            return null;
        }

        $length = strlen($digits);
        if ($length !== 11 && $length !== 14) {
            throw new ValidationException([
                'cnpj' => ['CPF deve conter 11 dígitos ou CNPJ deve conter 14 dígitos.'],
            ]);
        }

        return $digits;
    }
}
