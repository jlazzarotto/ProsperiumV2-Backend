<?php

declare(strict_types=1);

namespace App\Company\Application\DTO;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\TenantInstance;

final class CompanyResponse
{
    public static function fromEntity(Company $company, TenantInstance $tenantInstance): array
    {
        return [
            'id' => $company->getId(),
            'nome' => $company->getNome(),
            'status' => $company->getStatus(),
            'createdAt' => $company->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updatedAt' => $company->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            'tenantInstance' => [
                'id' => $tenantInstance->getId(),
                'tenancyMode' => $tenantInstance->getTenancyMode(),
                'databaseKey' => $tenantInstance->getDatabaseKey(),
                'status' => $tenantInstance->getStatus(),
            ],
        ];
    }
}
