<?php

declare(strict_types=1);

namespace App\Company\Application\Service;

use App\Company\Application\DTO\CreateCompanyRequest;
use App\Company\Application\DTO\UpdateCompanyRequest;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\TenantInstance;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\TenantInstanceRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Contract\TenantDatabaseRegistryInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;

final class CompanyService
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly TenantInstanceRepositoryInterface $tenantInstanceRepository,
        private readonly TenantDatabaseRegistryInterface $tenantDatabaseRegistry,
        private readonly RequestValidator $requestValidator,
        private readonly TransactionRunnerInterface $transactionRunner,
        private readonly AuditoriaLogger $auditoriaLogger,
        private readonly TenantCatalogProjector $tenantCatalogProjector
    ) {
    }

    /**
     * @return array{company: Company, tenantInstance: TenantInstance}
     */
    public function create(CreateCompanyRequest $request): array
    {
        $this->requestValidator->validate($request);

        $databaseKey = $request->databaseKey !== null ? trim($request->databaseKey) : '';

        if ($databaseKey === '') {
            throw new ValidationException([
                'databaseKey' => ['databaseKey é obrigatório para todos os tenants.'],
            ]);
        }

        if (!$this->tenantDatabaseRegistry->hasDatabaseKey($databaseKey) || $this->tenantDatabaseRegistry->findDatabaseUrl($databaseKey) === null) {
            throw new ValidationException([
                'databaseKey' => ['databaseKey não está configurado em config/tenants.yaml.'],
            ]);
        }

        if ($this->tenantInstanceRepository->existsByDatabaseKey($databaseKey)) {
            throw new ValidationException([
                'databaseKey' => ['databaseKey já está em uso.'],
            ]);
        }

        $result = $this->transactionRunner->run(function () use ($request, $databaseKey): array {
            $company = new Company($request->nome, $request->status);
            $tenantInstance = new TenantInstance(
                $company,
                $request->tenancyMode,
                $databaseKey,
                $request->status
            );

            $this->companyRepository->save($company);
            $this->tenantInstanceRepository->save($tenantInstance);
            $this->auditoriaLogger->log(
                $company->getId(),
                'company',
                'company.created',
                [
                    'companyId' => $company->getId(),
                    'tenancyMode' => $tenantInstance->getTenancyMode(),
                    'databaseKey' => $tenantInstance->getDatabaseKey(),
                ]
            );

            return [
                'company' => $company,
                'tenantInstance' => $tenantInstance,
            ];
        });

        $this->tenantCatalogProjector->syncCompany($result['company'], $databaseKey);

        return $result;
    }

    public function hasCompanies(): bool
    {
        return $this->companyRepository->countAll() > 0;
    }

    /**
     * @return list<array{company: Company, tenantInstance: TenantInstance}>
     */
    public function list(?string $status = null): array
    {
        $items = [];

        foreach ($this->companyRepository->listAll($status) as $company) {
            $tenantInstance = $this->tenantInstanceRepository->findByCompanyId((int) $company->getId());

            if ($tenantInstance === null) {
                continue;
            }

            $items[] = [
                'company' => $company,
                'tenantInstance' => $tenantInstance,
            ];
        }

        return $items;
    }

    /**
     * @return array{company: Company, tenantInstance: TenantInstance}
     */
    public function getById(int $id): array
    {
        $company = $this->companyRepository->findById($id);

        if ($company === null) {
            throw new ResourceNotFoundException('Company não encontrada.');
        }

        $tenantInstance = $this->tenantInstanceRepository->findByCompanyId($id);

        if ($tenantInstance === null) {
            throw new ResourceNotFoundException('TenantInstance não encontrada para a Company informada.');
        }

        return [
            'company' => $company,
            'tenantInstance' => $tenantInstance,
        ];
    }

    /**
     * @return array{company: Company, tenantInstance: TenantInstance}
     */
    public function update(int $id, UpdateCompanyRequest $request): array
    {
        $this->requestValidator->validate($request);

        $result = $this->getById($id);
        $company = $result['company'];
        $tenantInstance = $result['tenantInstance'];
        $databaseKey = $request->databaseKey !== null ? trim($request->databaseKey) : '';

        if ($databaseKey !== '' && $databaseKey !== $tenantInstance->getDatabaseKey()) {
            throw new ValidationException([
                'databaseKey' => ['Alteração de databaseKey exige migração formal do tenant.'],
            ]);
        }

        if ($databaseKey === '') {
            throw new ValidationException([
                'databaseKey' => ['databaseKey é obrigatório para todos os tenants.'],
            ]);
        }

        if (!$this->tenantDatabaseRegistry->hasDatabaseKey($databaseKey) || $this->tenantDatabaseRegistry->findDatabaseUrl($databaseKey) === null) {
            throw new ValidationException([
                'databaseKey' => ['databaseKey não está configurado em config/tenants.yaml.'],
            ]);
        }

        $existingTenantInstance = $this->tenantInstanceRepository->findByDatabaseKey($databaseKey);
        if ($existingTenantInstance !== null && (int) $existingTenantInstance->getId() !== (int) $tenantInstance->getId()) {
            throw new ValidationException([
                'databaseKey' => ['databaseKey já está em uso.'],
            ]);
        }

        $result = $this->transactionRunner->run(function () use ($company, $tenantInstance, $request, $databaseKey): array {
            $company->update($request->nome, $request->status);
            $tenantInstance->update($request->tenancyMode, $databaseKey, $request->status);

            $this->companyRepository->save($company);
            $this->tenantInstanceRepository->save($tenantInstance);
            $this->auditoriaLogger->log(
                (int) $company->getId(),
                'company',
                'company.updated',
                [
                    'companyId' => $company->getId(),
                    'tenancyMode' => $tenantInstance->getTenancyMode(),
                    'databaseKey' => $tenantInstance->getDatabaseKey(),
                    'status' => $company->getStatus(),
                ]
            );

            return [
                'company' => $company,
                'tenantInstance' => $tenantInstance,
            ];
        });

        $this->tenantCatalogProjector->syncCompany($result['company'], $databaseKey);

        return $result;
    }
}
