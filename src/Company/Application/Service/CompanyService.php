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
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;

final class CompanyService
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly TenantInstanceRepositoryInterface $tenantInstanceRepository,
        private readonly RequestValidator $requestValidator,
        private readonly TransactionRunnerInterface $transactionRunner,
        private readonly AuditoriaLogger $auditoriaLogger
    ) {
    }

    /**
     * @return array{company: Company, tenantInstance: TenantInstance}
     */
    public function create(CreateCompanyRequest $request): array
    {
        $this->requestValidator->validate($request);

        $databaseKey = $request->databaseKey !== null ? trim($request->databaseKey) : '';

        if ($request->tenancyMode === 'dedicated' && $databaseKey === '') {
            throw new ValidationException([
                'databaseKey' => ['databaseKey é obrigatório para tenancy dedicada.'],
            ]);
        }

        if ($databaseKey !== '' && $this->tenantInstanceRepository->existsByDatabaseKey($databaseKey)) {
            throw new ValidationException([
                'databaseKey' => ['databaseKey já está em uso.'],
            ]);
        }

        return $this->transactionRunner->run(function () use ($request, $databaseKey): array {
            $company = new Company($request->nome, $request->status);
            $tenantInstance = new TenantInstance(
                $company,
                $request->tenancyMode,
                $databaseKey !== '' ? $databaseKey : sprintf('shared-company-%s', strtolower(bin2hex(random_bytes(6)))),
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

        if ($request->tenancyMode === 'dedicated' && $databaseKey === '') {
            throw new ValidationException([
                'databaseKey' => ['databaseKey é obrigatório para tenancy dedicada.'],
            ]);
        }

        if ($databaseKey === '') {
            $databaseKey = $request->tenancyMode === 'shared'
                ? $tenantInstance->getDatabaseKey()
                : '';
        }

        $existingTenantInstance = $this->tenantInstanceRepository->findByDatabaseKey($databaseKey);
        if ($databaseKey !== '' && $existingTenantInstance !== null && $existingTenantInstance->getId() !== $tenantInstance->getId()) {
            throw new ValidationException([
                'databaseKey' => ['databaseKey já está em uso.'],
            ]);
        }

        return $this->transactionRunner->run(function () use ($company, $tenantInstance, $request, $databaseKey): array {
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
    }
}
