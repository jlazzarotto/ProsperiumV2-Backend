<?php

declare(strict_types=1);

namespace App\Integracao\Psp\Application\Service;

final class PspDuvService
{
    public function __construct(
        private readonly PspApiClient $client,
        private readonly PspConsultaHistoricoService $historicoService,
    )
    {
    }

    /**
     * @param array<string, scalar|bool|null> $filters
     *
     * @return array<string, mixed>|list<mixed>
     */
    public function listDuvs(array $filters): array
    {
        return $this->track('duvs.list', $filters, fn (): array => $this->client->get('/psp-cdp-rest/api/duvs', $filters));
    }

    /**
     * @return array<string, mixed>|list<mixed>
     */
    public function getResumo(int $numeroDuv): array
    {
        return $this->track('duv.resumo', ['numeroDuv' => $numeroDuv], fn (): array => $this->client->get(sprintf('/psp-cdp-rest/api/duv/%d/resumo', $numeroDuv)));
    }

    /**
     * @return array<string, mixed>|list<mixed>
     */
    public function getEmbarcacao(int $numeroDuv): array
    {
        return $this->track('duv.embarcacao', ['numeroDuv' => $numeroDuv], fn (): array => $this->client->get(sprintf('/psp-cdp-rest/api/duv/%d/embarcacao', $numeroDuv)));
    }

    /**
     * @return array<string, mixed>|list<mixed>
     */
    public function getAnuencias(int $numeroDuv): array
    {
        return $this->track('duv.anuencias', ['numeroDuv' => $numeroDuv], fn (): array => $this->client->get(sprintf('/psp-cdp-rest/api/duv/%d/anuencias', $numeroDuv)));
    }

    /**
     * @return array<string, mixed>|list<mixed>
     */
    public function getChegadasSaidas(int $numeroDuv, bool $v2 = true): array
    {
        $suffix = $v2 ? '/v2' : '';
        return $this->track('duv.chegadas_saidas', ['numeroDuv' => $numeroDuv, 'v2' => $v2], fn (): array => $this->client->get(sprintf('/psp-cdp-rest/api/duv/%d/chegadas-saidas%s', $numeroDuv, $suffix)));
    }

    /**
     * @return array<string, mixed>|list<mixed>
     */
    public function getAnexos(int $numeroDuv): array
    {
        return $this->track('duv.anexos', ['numeroDuv' => $numeroDuv], fn (): array => $this->client->get(sprintf('/psp-cdp-rest/api/duv/%d/anexos', $numeroDuv)));
    }

    /**
     * @return array<string, mixed>|list<mixed>
     */
    public function getLocaisAtracacao(string $bitrigramaPorto): array
    {
        return $this->track('cadastro.locais_atracacao', ['bitrigramaPorto' => strtolower($bitrigramaPorto)], fn (): array => $this->client->get(sprintf('/psp-cdp-rest/api/cadastro/%s/locais-atracacao-porto', strtolower($bitrigramaPorto))));
    }

    /**
     * @return array<string, mixed>
     */
    public function testAuthentication(): array
    {
        return $this->track('auth.status', [], fn (): array => $this->client->testAuthentication());
    }

    /**
     * @template T of array<string, mixed>|list<mixed>
     * @param array<string, mixed> $request
     * @param \Closure():T $callback
     * @return T
     */
    private function track(string $endpointKey, array $request, \Closure $callback): array
    {
        $startedAt = microtime(true);

        try {
            $response = $callback();
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
            $normalizedResponse = is_array($response) ? ['payload' => $response] : null;
            $this->historicoService->log($endpointKey, $request, $normalizedResponse, true, $durationMs);

            return $response;
        } catch (\Throwable $exception) {
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
            $this->historicoService->log($endpointKey, $request, null, false, $durationMs, $exception->getMessage());
            throw $exception;
        }
    }
}
