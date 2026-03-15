<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Auditoria;

use App\Shared\Domain\Contract\AuthenticatedUserProviderInterface;
use App\Shared\Infrastructure\MultiTenancy\TenantContext;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class AuditoriaLogger
{
    public function __construct(
        private readonly Connection $connection,
        private readonly ?AuthenticatedUserProviderInterface $authenticatedUserProvider = null,
        private readonly ?RequestStack $requestStack = null,
        private readonly ?TenantContext $tenantContext = null
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function log(?int $companyId, string $recurso, string $acao, array $payload = []): void
    {
        $request = $this->requestStack?->getCurrentRequest();
        $requestPayload = $this->extractRequestPayload($request);
        $authenticatedUser = $this->authenticatedUserProvider?->getUser();

        $this->connection->insert('auditoria_logs', [
            'company_id' => $companyId ?? $this->extractNullableInt($requestPayload['companyId'] ?? null) ?? $this->tenantContext?->getCompanyId(),
            'empresa_id' => $this->extractNullableInt($requestPayload['empresaId'] ?? null),
            'unidade_id' => $this->extractNullableInt($requestPayload['unidadeId'] ?? null),
            'user_id' => $authenticatedUser?->id,
            'recurso' => $recurso,
            'acao' => $acao,
            'payload_json' => json_encode($payload, JSON_THROW_ON_ERROR),
            'request_id' => $this->trimNullableString($request?->attributes->get('request_id')),
            'request_path' => $this->trimNullableString($request?->getPathInfo()),
            'request_method' => $this->trimNullableString($request?->getMethod()),
            'ip_address' => $this->trimNullableString($request?->getClientIp()),
            'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function extractRequestPayload(?Request $request): array
    {
        if ($request === null) {
            return [];
        }

        $payload = $request->query->all();
        $content = $request->getContent();

        if ($content !== '') {
            try {
                $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($decoded)) {
                    $payload = array_replace($payload, $decoded);
                }
            } catch (\JsonException) {
            }
        }

        return $payload;
    }

    private function extractNullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function trimNullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : null;
    }
}
