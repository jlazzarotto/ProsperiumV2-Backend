<?php

declare(strict_types=1);

namespace App\Company\Application\Service;

use App\Company\Application\DTO\CreateEmpresaRequest;
use App\Company\Application\DTO\UpdateEmpresaRequest;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Service\CnpjNormalizer;
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
        private readonly CnpjNormalizer $cnpjNormalizer,
        private readonly RequestValidator $requestValidator,
        private readonly TransactionRunnerInterface $transactionRunner,
        private readonly AuditoriaLogger $auditoriaLogger
    ) {
    }

    public function create(CreateEmpresaRequest $request): Empresa
    {
        $this->requestValidator->validate($request);

        $company = $this->companyRepository->findById((int) $request->companyId);

        if ($company === null) {
            throw new ResourceNotFoundException('Company não encontrada.');
        }

        $cnpj = $this->cnpjNormalizer->normalize($request->cnpj);

        if ($this->empresaRepository->existsByCompanyAndCnpj((int) $request->companyId, $cnpj)) {
            throw new ValidationException([
                'cnpj' => ['Já existe uma empresa com este CNPJ na company informada.'],
            ]);
        }

        return $this->transactionRunner->run(function () use ($request, $company, $cnpj): Empresa {
            $empresa = new Empresa($company, $request->razaoSocial, $request->nomeFantasia, $cnpj, $request->status);
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

        $cnpj = $this->cnpjNormalizer->normalize($request->cnpj);
        $existingEmpresaWithSameCnpj = $this->empresaRepository->listAll((int) $company->getId());

        foreach ($existingEmpresaWithSameCnpj as $item) {
            if ((int) $item->getId() !== (int) $empresa->getId() && $item->getCnpj() === $cnpj) {
                throw new ValidationException([
                    'cnpj' => ['Já existe uma empresa com este CNPJ na company informada.'],
                ]);
            }
        }

        return $this->transactionRunner->run(function () use ($empresa, $company, $request, $cnpj): Empresa {
            $empresa->update($company, $request->razaoSocial, $request->nomeFantasia, $cnpj, $request->status);
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
    }
}
