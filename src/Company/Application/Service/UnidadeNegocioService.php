<?php

declare(strict_types=1);

namespace App\Company\Application\Service;

use App\Company\Application\DTO\CreateUnidadeNegocioRequest;
use App\Company\Application\DTO\UpdateUnidadeNegocioRequest;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\TenantInstanceRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;

final class UnidadeNegocioService
{
    public function __construct(
        private readonly UnidadeNegocioRepositoryInterface $unidadeNegocioRepository,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly TenantInstanceRepositoryInterface $tenantInstanceRepository,
        private readonly RequestValidator $requestValidator,
        private readonly TransactionRunnerInterface $transactionRunner,
        private readonly AuditoriaLogger $auditoriaLogger,
        private readonly TenantCatalogProjector $tenantCatalogProjector
    ) {
    }

    public function create(CreateUnidadeNegocioRequest $request): UnidadeNegocio
    {
        $this->requestValidator->validate($request);

        $company = $this->companyRepository->findById((int) $request->companyId);

        if ($company === null) {
            throw new ResourceNotFoundException('Company não encontrada.');
        }

        if ($this->unidadeNegocioRepository->existsByCompanyAndNome((int) $request->companyId, $request->nome)) {
            throw new ValidationException([
                'nome' => ['Já existe uma unidade de negócio com este nome na company informada.'],
            ]);
        }

        if ($this->unidadeNegocioRepository->existsByCompanyAndAbreviatura((int) $request->companyId, $request->abreviatura)) {
            throw new ValidationException([
                'abreviatura' => ['Já existe uma unidade de negócio com esta abreviatura na company informada.'],
            ]);
        }

        $unidadeNegocio = $this->transactionRunner->run(function () use ($request, $company): UnidadeNegocio {
            $unidadeNegocio = new UnidadeNegocio($company, $request->nome, $request->abreviatura, $request->status);
            $this->unidadeNegocioRepository->save($unidadeNegocio);
            $this->auditoriaLogger->log(
                (int) $company->getId(),
                'unidade_negocio',
                'unidade_negocio.created',
                [
                    'unidadeId' => $unidadeNegocio->getId(),
                    'abreviatura' => $unidadeNegocio->getAbreviatura(),
                ]
            );

            return $unidadeNegocio;
        });

        $databaseKey = $this->tenantInstanceRepository->findByCompanyId((int) $company->getId())?->getDatabaseKey();
        if ($databaseKey === null) {
            throw new ResourceNotFoundException('TenantInstance não encontrada para a company informada.');
        }

        $this->tenantCatalogProjector->syncCompany($company, $databaseKey);
        $this->tenantCatalogProjector->syncUnidadeNegocio($unidadeNegocio);

        return $unidadeNegocio;
    }

    /**
     * @return list<UnidadeNegocio>
     */
    public function list(?int $companyId = null, ?string $status = null): array
    {
        return $this->unidadeNegocioRepository->listAll($companyId, $status);
    }

    public function getById(int $id): UnidadeNegocio
    {
        $unidadeNegocio = $this->unidadeNegocioRepository->findById($id);

        if ($unidadeNegocio === null) {
            throw new ResourceNotFoundException('Unidade de negócio não encontrada.');
        }

        return $unidadeNegocio;
    }

    public function update(int $id, UpdateUnidadeNegocioRequest $request): UnidadeNegocio
    {
        $this->requestValidator->validate($request);

        $unidadeNegocio = $this->unidadeNegocioRepository->findById($id);

        if ($unidadeNegocio === null) {
            throw new ResourceNotFoundException('Unidade de negócio não encontrada.');
        }

        $company = $this->companyRepository->findById((int) $request->companyId);

        if ($company === null) {
            throw new ResourceNotFoundException('Company não encontrada.');
        }

        if ($unidadeNegocio->getNome() !== $request->nome && $this->unidadeNegocioRepository->existsByCompanyAndNome((int) $request->companyId, $request->nome)) {
            throw new ValidationException([
                'nome' => ['Já existe uma unidade de negócio com este nome na company informada.'],
            ]);
        }

        if ($unidadeNegocio->getAbreviatura() !== $request->abreviatura && $this->unidadeNegocioRepository->existsByCompanyAndAbreviatura((int) $request->companyId, $request->abreviatura)) {
            throw new ValidationException([
                'abreviatura' => ['Já existe uma unidade de negócio com esta abreviatura na company informada.'],
            ]);
        }

        $unidadeNegocio = $this->transactionRunner->run(function () use ($unidadeNegocio, $request, $company): UnidadeNegocio {
            $unidadeNegocio->setNome($request->nome);
            $unidadeNegocio->setAbreviatura($request->abreviatura);
            $unidadeNegocio->setStatus($request->status);
            $unidadeNegocio->setCompany($company);
            $this->unidadeNegocioRepository->save($unidadeNegocio);
            $this->auditoriaLogger->log(
                (int) $company->getId(),
                'unidade_negocio',
                'unidade_negocio.updated',
                [
                    'unidadeId' => $unidadeNegocio->getId(),
                    'abreviatura' => $unidadeNegocio->getAbreviatura(),
                ]
            );

            return $unidadeNegocio;
        });

        $databaseKey = $this->tenantInstanceRepository->findByCompanyId((int) $company->getId())?->getDatabaseKey();
        if ($databaseKey === null) {
            throw new ResourceNotFoundException('TenantInstance não encontrada para a company informada.');
        }

        $this->tenantCatalogProjector->syncUnidadeNegocio($unidadeNegocio);

        return $unidadeNegocio;
    }
}
