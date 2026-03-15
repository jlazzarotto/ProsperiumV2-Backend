<?php

declare(strict_types=1);

namespace App\Company\Application\Service;

use App\Company\Application\DTO\CreateUnidadeNegocioRequest;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
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
        private readonly RequestValidator $requestValidator,
        private readonly TransactionRunnerInterface $transactionRunner,
        private readonly AuditoriaLogger $auditoriaLogger
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

        return $this->transactionRunner->run(function () use ($request, $company): UnidadeNegocio {
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
}
