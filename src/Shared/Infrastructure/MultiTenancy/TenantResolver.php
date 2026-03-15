<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\MultiTenancy;

use App\Company\Domain\Repository\TenantInstanceRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class TenantResolver
{
    public function __construct(private readonly TenantInstanceRepositoryInterface $tenantInstanceRepository)
    {
    }

    /**
     * @return array{tenantId:?string,companyId:?int}
     */
    public function resolve(Request $request): array
    {
        $tenantId = $request->headers->get('X-Tenant-Id');
        $companyHeader = $request->headers->get('X-Company-Id');

        if (is_string($companyHeader) && ctype_digit($companyHeader)) {
            return ['tenantId' => $tenantId, 'companyId' => (int) $companyHeader];
        }

        if (is_string($tenantId) && ctype_digit($tenantId)) {
            return ['tenantId' => $tenantId, 'companyId' => (int) $tenantId];
        }

        if (is_string($tenantId) && trim($tenantId) !== '') {
            $tenantInstance = $this->tenantInstanceRepository->findByDatabaseKey($tenantId);

            if ($tenantInstance !== null && $tenantInstance->getCompany()->getId() !== null) {
                return ['tenantId' => $tenantId, 'companyId' => (int) $tenantInstance->getCompany()->getId()];
            }
        }

        return ['tenantId' => $tenantId, 'companyId' => null];
    }
}
