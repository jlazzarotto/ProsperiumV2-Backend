<?php

declare(strict_types=1);

namespace App\Configuracao\Application\Service;

use App\Configuracao\Application\DTO\SaveConfigParamRequest;
use App\Configuracao\Application\DTO\UpdateConfigParamRestrictRequest;
use App\Configuracao\Application\DTO\UpdateConfigParamStatusRequest;
use App\Configuracao\Domain\Entity\ConfigParam;
use App\Configuracao\Domain\Repository\ConfigParamRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;

final class ConfigParamService
{
    public function __construct(
        private readonly ConfigParamRepositoryInterface $repo,
        private readonly RequestValidator $validator,
        private readonly TransactionRunnerInterface $tx,
        private readonly AuditoriaLogger $audit,
    ) {
    }

    /** @return array{data: list<ConfigParam>, types: list<string>} */
    public function list(int $companyId): array
    {
        return [
            'data' => $this->repo->listAll($companyId),
            'types' => $this->repo->listDistinctTypes($companyId),
        ];
    }

    public function save(SaveConfigParamRequest $request): ConfigParam
    {
        $this->validator->validate($request);

        $companyId = (int) $request->companyId;
        $company = $this->repo->getCompanyReference($companyId);

        $originalName = $request->originalName !== null ? trim($request->originalName) : null;
        $referenceName = ($originalName !== null && $originalName !== '') ? $originalName : $request->name;

        $existing = $this->repo->findByCompanyAndName($companyId, $referenceName);

        if ($existing) {
            if ($referenceName !== trim($request->name)) {
                $duplicate = $this->repo->findByCompanyAndName($companyId, trim($request->name));
                if ($duplicate) {
                    throw new ValidationException(['name' => ['Já existe um parâmetro com este nome.']]);
                }
            }

            return $this->tx->run(function () use ($request, $existing, $companyId): ConfigParam {
                $existing->update(
                    $request->name,
                    $request->value,
                    $request->type,
                    $request->description,
                );
                $this->repo->save($existing);
                $this->audit->log(
                    $companyId,
                    'config_param',
                    'configuracao.config_param.updated',
                    ['configParamId' => $existing->getId(), 'name' => $existing->getName()],
                );
                return $existing;
            });
        }

        $duplicate = $this->repo->findByCompanyAndName($companyId, trim($request->name));
        if ($duplicate) {
            throw new ValidationException(['name' => ['Já existe um parâmetro com este nome.']]);
        }

        return $this->tx->run(function () use ($request, $company, $companyId): ConfigParam {
            $param = new ConfigParam(
                $companyId,
                $request->name,
                $request->value,
                $request->type,
                $request->description,
                status: 1,  // Default: ativado
                restrict: 1, // Default: COM restrição
            );
            $this->repo->save($param);
            $this->audit->log(
                $companyId,
                'config_param',
                'configuracao.config_param.created',
                ['configParamId' => $param->getId(), 'name' => $param->getName()],
            );
            return $param;
        });
    }

    public function updateStatus(UpdateConfigParamStatusRequest $request): ConfigParam
    {
        $this->validator->validate($request);

        $param = $this->repo->findByCompanyAndName((int) $request->companyId, $request->name);
        if (!$param) {
            throw new ResourceNotFoundException('Parâmetro não encontrado.');
        }

        return $this->tx->run(function () use ($request, $param): ConfigParam {
            $param->updateStatus((int) $request->status);
            $this->repo->save($param);
            $this->audit->log(
                (int) $request->companyId,
                'config_param',
                'configuracao.config_param.status_updated',
                ['configParamId' => $param->getId(), 'name' => $param->getName(), 'status' => $param->getStatus()],
            );
            return $param;
        });
    }

    public function updateRestrict(UpdateConfigParamRestrictRequest $request): ConfigParam
    {
        $this->validator->validate($request);

        $param = $this->repo->findByCompanyAndName((int) $request->companyId, $request->name);
        if (!$param) {
            throw new ResourceNotFoundException('Parâmetro não encontrado.');
        }

        return $this->tx->run(function () use ($request, $param): ConfigParam {
            $param->updateRestrict((int) $request->restrict);
            $this->repo->save($param);
            $this->audit->log(
                (int) $request->companyId,
                'config_param',
                'configuracao.config_param.restrict_updated',
                ['configParamId' => $param->getId(), 'name' => $param->getName(), 'restrict' => $param->getRestrict()],
            );
            return $param;
        });
    }
}
