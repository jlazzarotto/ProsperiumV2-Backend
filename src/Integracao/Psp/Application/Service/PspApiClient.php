<?php

declare(strict_types=1);

namespace App\Integracao\Psp\Application\Service;

final class PspApiClient
{
    private const TOKEN_TTL_SECONDS = 1740;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $chave,
        private readonly string $senha,
        private readonly string $projectDir,
    ) {
    }

    /**
     * @param array<string, scalar|bool|null> $query
     *
     * @return array<string, mixed>|list<mixed>
     */
    public function get(string $path, array $query = []): array
    {
        $url = $this->buildUrl($path, $query);

        return $this->requestJson('GET', $url, null, [
            'Authorization: Bearer ' . $this->getToken(),
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>|list<mixed>
     */
    public function post(string $path, array $payload): array
    {
        $url = $this->buildUrl($path);

        return $this->requestJson('POST', $url, $payload, [
            'Authorization: Bearer ' . $this->getToken(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function testAuthentication(): array
    {
        $token = $this->authenticate();

        return [
            'authenticated' => true,
            'tokenPreview' => substr($token, 0, 24) . '...',
        ];
    }

    private function getToken(): string
    {
        $cached = $this->readTokenCache();
        if ($cached !== null) {
            return $cached;
        }

        $token = $this->authenticate();
        $this->writeTokenCache($token);

        return $token;
    }

    private function authenticate(): string
    {
        $this->assertConfigured();

        $response = $this->requestJson(
            'POST',
            $this->buildRawUrl('/psp-autenticacao-rest-integracao/api/autenticacao/chave-senha'),
            [
                'chave' => $this->chave,
                'senha' => $this->senha,
            ],
        );

        $token = $response['pspJwt'] ?? null;
        if (!is_string($token) || $token === '') {
            throw new \RuntimeException('A autenticação PSP não retornou um token válido.');
        }

        return $token;
    }

    private function assertConfigured(): void
    {
        if ($this->baseUrl === '' || $this->chave === '' || $this->senha === '') {
            throw new \RuntimeException('Configuração PSP incompleta. Defina PSP_API_BASE_URL, PSP_API_CHAVE e PSP_API_SENHA.');
        }
    }

    /**
     * @param array<string, scalar|bool|null> $query
     */
    private function buildUrl(string $path, array $query = []): string
    {
        $url = $this->buildRawUrl($path);
        $query = array_filter($query, static fn (mixed $value): bool => $value !== null && $value !== '');

        if ($query === []) {
            return $url;
        }

        return $url . '?' . http_build_query($query);
    }

    private function buildRawUrl(string $path): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }

    /**
     * @param array<string, mixed>|null $payload
     * @param list<string> $extraHeaders
     *
     * @return array<string, mixed>|list<mixed>
     */
    private function requestJson(string $method, string $url, ?array $payload = null, array $extraHeaders = []): array
    {
        $headers = array_merge([
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: ProsperiumPsp/1.0',
        ], $extraHeaders);

        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'timeout' => 30,
                'ignore_errors' => true,
                'header' => implode("\r\n", $headers),
                'content' => $payload !== null ? json_encode($payload, JSON_THROW_ON_ERROR) : null,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            throw new \RuntimeException('Falha ao consultar a API do PSP.');
        }

        $statusCode = $this->extractStatusCode($http_response_header ?? []);
        if ($statusCode >= 400) {
            throw new \RuntimeException(sprintf('A API do PSP respondeu com HTTP %d: %s', $statusCode, $response));
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            throw new \RuntimeException('Resposta inválida da API do PSP.');
        }

        return $data;
    }

    /**
     * @param list<string> $headers
     */
    private function extractStatusCode(array $headers): int
    {
        foreach ($headers as $header) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})\b/', $header, $matches) === 1) {
                return (int) $matches[1];
            }
        }

        return 200;
    }

    private function getCacheFile(): string
    {
        return $this->projectDir . '/var/cache/psp-jwt.json';
    }

    private function readTokenCache(): ?string
    {
        $cacheFile = $this->getCacheFile();
        if (!is_file($cacheFile)) {
            return null;
        }

        $payload = json_decode((string) file_get_contents($cacheFile), true);
        if (!is_array($payload)) {
            return null;
        }

        $token = $payload['token'] ?? null;
        $obtainedAt = $payload['obtainedAt'] ?? null;
        if (!is_string($token) || !is_int($obtainedAt)) {
            return null;
        }

        if ((time() - $obtainedAt) >= self::TOKEN_TTL_SECONDS) {
            return null;
        }

        return $token;
    }

    private function writeTokenCache(string $token): void
    {
        $cacheFile = $this->getCacheFile();
        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0775, true);
        }

        file_put_contents($cacheFile, json_encode([
            'token' => $token,
            'obtainedAt' => time(),
        ], JSON_THROW_ON_ERROR));
    }
}
