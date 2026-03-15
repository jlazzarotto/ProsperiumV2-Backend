<?php

declare(strict_types=1);

namespace App\Integracao\Psp\Application\Service;

use App\Integracao\Psp\Domain\Entity\PspConsultaHistorico;
use App\Integracao\Psp\Domain\Repository\PspConsultaHistoricoRepositoryInterface;
use App\Shared\Domain\Contract\AuthenticatedUserProviderInterface;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use App\Shared\Infrastructure\MultiTenancy\TenantContext;

final class PspConsultaHistoricoService
{
    public function __construct(
        private readonly PspConsultaHistoricoRepositoryInterface $repo,
        private readonly AuditoriaLogger $auditoriaLogger,
        private readonly ?AuthenticatedUserProviderInterface $authenticatedUserProvider = null,
        private readonly ?TenantContext $tenantContext = null,
    ) {
    }

    /**
     * @param array<string, mixed> $request
     * @param array<string, mixed>|null $response
     */
    public function log(string $endpointKey, array $request, ?array $response, bool $success, int $durationMs, ?string $errorMessage = null): void
    {
        $companyId = $this->tenantContext?->getCompanyId();
        $userId = $this->authenticatedUserProvider?->getUser()?->id;

        $historico = new PspConsultaHistorico(
            $companyId,
            $userId,
            $endpointKey,
            $request,
            $response,
            $success,
            $durationMs,
            $errorMessage,
        );

        $this->repo->save($historico);
        $this->auditoriaLogger->log($companyId, 'integracao_psp', 'integracao.psp.consulta', [
            'endpointKey' => $endpointKey,
            'success' => $success,
            'durationMs' => $durationMs,
            'errorMessage' => $errorMessage,
        ]);
    }

    /** @return list<PspConsultaHistorico> */
    public function listRecent(?string $endpointKey = null, int $limit = 30): array
    {
        return $this->repo->listRecent($this->tenantContext?->getCompanyId(), $endpointKey, $limit);
    }
}
