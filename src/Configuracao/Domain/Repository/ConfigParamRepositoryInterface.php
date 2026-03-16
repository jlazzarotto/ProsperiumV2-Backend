<?php

declare(strict_types=1);

namespace App\Configuracao\Domain\Repository;

use App\Company\Domain\Entity\Company;
use App\Configuracao\Domain\Entity\ConfigParam;

interface ConfigParamRepositoryInterface
{
    public function save(ConfigParam $param): void;

    public function findById(int $id): ?ConfigParam;

    public function findByCompanyAndName(int $companyId, string $name): ?ConfigParam;

    /** @return list<ConfigParam> */
    public function listAll(int $companyId): array;

    /** @return list<string> */
    public function listDistinctTypes(int $companyId): array;

    public function getCompanyReference(int $companyId): Company;
}
