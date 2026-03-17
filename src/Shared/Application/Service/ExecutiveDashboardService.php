<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\TenantInstanceRepositoryInterface;
use App\Shared\Domain\Contract\TenantDatabaseRegistryInterface;
use App\Shared\Infrastructure\MultiTenancy\TenantConnectionRuntime;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Serviço para gerar dashboard executivo com dados agregados de todos os tenants
 * Sem necessidade de company_id selecionado (ROLE_ROOT)
 */
final class ExecutiveDashboardService
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly TenantInstanceRepositoryInterface $tenantInstanceRepository,
        private readonly TenantDatabaseRegistryInterface $tenantDatabaseRegistry,
        private readonly EntityManagerInterface $tenantEntityManager,
    ) {
    }

    /**
     * @return array{activeCompanies: int, empresas: array{total: int, active: int}, unidades: array{total: int, active: int}, sharedCompanies: int, dedicatedCompanies: int}
     */
    public function getAggregatedMetrics(): array
    {
        $empresasTotal = 0;
        $empresasActive = 0;
        $unidadesTotal = 0;
        $unidadesActive = 0;
        $sharedCount = 0;
        $dedicatedCount = 0;
        $processedDatabaseKeys = []; // Rastrear database_keys já processados

        // Contar companies ativas
        $activeCompanies = \count($this->companyRepository->listAll('active'));

        // Buscar todos os tenants ativos
        $tenants = $this->tenantInstanceRepository->findAllActive();

        foreach ($tenants as $tenant) {
            $databaseKey = $tenant->getDatabaseKey();
            $databaseUrl = $this->tenantDatabaseRegistry->findDatabaseUrl($databaseKey);

            if ($databaseUrl === null) {
                continue;
            }

            // Pular se já processamos este database_key (evita duplicação quando há múltiplos tenant instances com mesma database_key)
            if (\in_array($databaseKey, $processedDatabaseKeys, true)) {
                continue;
            }
            $processedDatabaseKeys[] = $databaseKey;

            // Contar tenancy mode (após verificar se já foi processado)
            $tenancyMode = $tenant->getTenancyMode();
            if ($tenancyMode === 'shared') {
                $sharedCount++;
            } elseif ($tenancyMode === 'dedicated') {
                $dedicatedCount++;
            }

            try {
                // Configurar runtime para conectar ao tenant DB
                TenantConnectionRuntime::setDatabaseUrl($databaseUrl);

                // Reconnect para aplicar a nova URL
                $conn = $this->tenantEntityManager->getConnection();
                if ($conn->isConnected()) {
                    $conn->close();
                }
                $conn->connect();

                // Contar empresas
                $empresasMetrics = $this->countEmpresasInTenant($conn);
                $empresasTotal += $empresasMetrics['total'];
                $empresasActive += $empresasMetrics['active'];

                // Contar unidades
                $unidadesMetrics = $this->countUnidadesInTenant($conn);
                $unidadesTotal += $unidadesMetrics['total'];
                $unidadesActive += $unidadesMetrics['active'];
            } catch (\Exception $e) {
                // Log erro mas continua com próximo tenant
                error_log("Erro ao agregar metrics para tenant {$databaseKey}: " . $e->getMessage());
                continue;
            } finally {
                // Reset runtime
                TenantConnectionRuntime::reset();
            }
        }

        return [
            'activeCompanies' => $activeCompanies,
            'empresas' => [
                'total' => $empresasTotal,
                'active' => $empresasActive,
            ],
            'unidades' => [
                'total' => $unidadesTotal,
                'active' => $unidadesActive,
            ],
            'sharedCompanies' => $sharedCount,
            'dedicatedCompanies' => $dedicatedCount,
        ];
    }

    /**
     * @return array{total: int, active: int}
     */
    private function countEmpresasInTenant(Connection $conn): array
    {
        try {
            // Total de empresas (usando DISTINCT para evitar duplicação por JOINs)
            $totalResult = $conn->fetchOne(
                'SELECT COUNT(DISTINCT empresas.id) FROM empresas'
            );
            $total = (int) $totalResult;

            // Empresas ativas (status = "active" e deleted_at IS NULL)
            $activeResult = $conn->fetchOne(
                "SELECT COUNT(DISTINCT empresas.id) FROM empresas WHERE status = 'active' AND deleted_at IS NULL"
            );
            $active = (int) $activeResult;

            return ['total' => $total, 'active' => $active];
        } catch (\Exception) {
            return ['total' => 0, 'active' => 0];
        }
    }

    /**
     * @return array{total: int, active: int}
     */
    private function countUnidadesInTenant(Connection $conn): array
    {
        try {
            // Total de unidades (usando DISTINCT para evitar duplicação por JOINs)
            $totalResult = $conn->fetchOne(
                'SELECT COUNT(DISTINCT unidades_negocio.id) FROM unidades_negocio'
            );
            $total = (int) $totalResult;

            // Unidades ativas (status = "active" e deleted_at IS NULL)
            $activeResult = $conn->fetchOne(
                "SELECT COUNT(DISTINCT unidades_negocio.id) FROM unidades_negocio WHERE status = 'active' AND deleted_at IS NULL"
            );
            $active = (int) $activeResult;

            return ['total' => $total, 'active' => $active];
        } catch (\Exception) {
            return ['total' => 0, 'active' => 0];
        }
    }
}
