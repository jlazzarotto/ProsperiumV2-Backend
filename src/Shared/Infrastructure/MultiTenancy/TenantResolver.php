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

        // Try to resolve from query string (companyId parameter)
        $queryCompanyId = $request->query->get('companyId');
        if (is_string($queryCompanyId) && ctype_digit($queryCompanyId)) {
            return ['tenantId' => $tenantId, 'companyId' => (int) $queryCompanyId];
        }

        // Try to resolve from JSON payload (companyId in body)
        $payload = $this->decodeJsonPayload($request);
        if ($payload !== null && isset($payload['companyId'])) {
            $payloadCompanyId = $payload['companyId'];
            if (is_int($payloadCompanyId) && $payloadCompanyId > 0) {
                return ['tenantId' => $tenantId, 'companyId' => $payloadCompanyId];
            }
            if (is_string($payloadCompanyId) && ctype_digit($payloadCompanyId)) {
                return ['tenantId' => $tenantId, 'companyId' => (int) $payloadCompanyId];
            }
        }

        return ['tenantId' => $tenantId, 'companyId' => null];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJsonPayload(Request $request): ?array
    {
        $contentType = (string) $request->headers->get('Content-Type', '');
        if ($contentType !== '' && !str_contains($contentType, 'json')) {
            return null;
        }

        $content = $request->getContent();
        if ($content === '') {
            return null;
        }

        try {
            $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return is_array($payload) ? $payload : null;
    }
}
