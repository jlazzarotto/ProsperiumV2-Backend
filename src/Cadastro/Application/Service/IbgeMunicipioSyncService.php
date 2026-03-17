<?php

declare(strict_types=1);

namespace App\Cadastro\Application\Service;

use App\Cadastro\Domain\Entity\Referencia\Municipio;
use App\Cadastro\Domain\Repository\MunicipioRepositoryInterface;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use Doctrine\ORM\EntityManagerInterface;

final class IbgeMunicipioSyncService
{
    private const ENDPOINT = 'https://servicodados.ibge.gov.br/api/v1/localidades/municipios';

    public function __construct(
        private readonly MunicipioRepositoryInterface $repo,
        private readonly TransactionRunnerInterface $tx,
        private readonly EntityManagerInterface $entityManager,
        private readonly AuditoriaLogger $audit,
    ) {
    }

    public function sync(): array
    {
        $payload = $this->fetchMunicipios();
        $existing = [];
        foreach ($this->repo->listAll() as $municipio) {
            $existing[$municipio->getCodigoIbge()] = $municipio;
        }

        $seen = [];
        $created = 0;
        $updated = 0;
        $unchanged = 0;

        $this->tx->run(function () use ($payload, $existing, &$seen, &$created, &$updated, &$unchanged): void {
            foreach ($payload as $item) {
                $normalized = $this->normalizeMunicipio($item);
                $codigoIbge = $normalized['codigoIbge'];
                $hash = $normalized['hashPayload'];
                $seen[$codigoIbge] = true;

                $current = $existing[$codigoIbge] ?? null;
                if ($current === null) {
                    $municipio = new Municipio(
                        $normalized['codigoIbge'],
                        $normalized['nome'],
                        $normalized['ufCodigoIbge'],
                        $normalized['ufSigla'],
                        $normalized['ufNome'],
                        $normalized['regiaoCodigoIbge'],
                        $normalized['regiaoSigla'],
                        $normalized['regiaoNome'],
                        $normalized['regiaoIntermediariaCodigoIbge'],
                        $normalized['regiaoIntermediariaNome'],
                        $normalized['regiaoImediataCodigoIbge'],
                        $normalized['regiaoImediataNome'],
                        $normalized['microrregiaoCodigoIbge'],
                        $normalized['microrregiaoNome'],
                        $normalized['mesorregiaoCodigoIbge'],
                        $normalized['mesorregiaoNome'],
                        $hash,
                    );
                    $this->entityManager->persist($municipio);
                    ++$created;
                    continue;
                }

                if ($current->getHashPayload() === $hash) {
                    ++$unchanged;
                    continue;
                }

                $current->updateFromSync(
                    $normalized['nome'],
                    $normalized['ufCodigoIbge'],
                    $normalized['ufSigla'],
                    $normalized['ufNome'],
                    $normalized['regiaoCodigoIbge'],
                    $normalized['regiaoSigla'],
                    $normalized['regiaoNome'],
                    $normalized['regiaoIntermediariaCodigoIbge'],
                    $normalized['regiaoIntermediariaNome'],
                    $normalized['regiaoImediataCodigoIbge'],
                    $normalized['regiaoImediataNome'],
                    $normalized['microrregiaoCodigoIbge'],
                    $normalized['microrregiaoNome'],
                    $normalized['mesorregiaoCodigoIbge'],
                    $normalized['mesorregiaoNome'],
                    $hash,
                );
                ++$updated;
            }

            foreach ($existing as $codigoIbge => $municipio) {
                if (!isset($seen[$codigoIbge])) {
                    $municipio->markInactive();
                    ++$updated;
                }
            }

            $this->entityManager->flush();
        });

        $total = \count($payload);
        $inactive = \count($existing) - \count($seen);
        return [
            'total' => $total,
            'created' => $created,
            'updated' => $updated,
            'unchanged' => $unchanged,
            'inactive' => $inactive,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function fetchMunicipios(): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 30,
                'header' => implode("\r\n", [
                    'Accept: application/json',
                    'Accept-Encoding: gzip',
                    'User-Agent: ProsperiumSync/1.0',
                ]),
            ],
        ]);

        $response = @file_get_contents(self::ENDPOINT, false, $context);
        if ($response === false) {
            throw new \RuntimeException('Falha ao consultar a API de municípios do IBGE.');
        }

        if (str_starts_with($response, "\x1f\x8b")) {
            $decoded = gzdecode($response);
            if ($decoded !== false) {
                $response = $decoded;
            }
        }

        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new \RuntimeException('Resposta inválida da API de municípios do IBGE.');
        }

        return $data;
    }

    /** @param array<string, mixed> $item */
    private function normalizeMunicipio(array $item): array
    {
        $uf = $item['regiao-imediata']['regiao-intermediaria']['UF']
            ?? $item['microrregiao']['mesorregiao']['UF']
            ?? null;
        $regiao = $uf['regiao'] ?? null;

        $normalized = [
            'codigoIbge' => (int) $item['id'],
            'nome' => (string) $item['nome'],
            'ufCodigoIbge' => (int) ($uf['id'] ?? 0),
            'ufSigla' => (string) ($uf['sigla'] ?? ''),
            'ufNome' => (string) ($uf['nome'] ?? ''),
            'regiaoCodigoIbge' => (int) ($regiao['id'] ?? 0),
            'regiaoSigla' => (string) ($regiao['sigla'] ?? ''),
            'regiaoNome' => (string) ($regiao['nome'] ?? ''),
            'regiaoIntermediariaCodigoIbge' => isset($item['regiao-imediata']['regiao-intermediaria']['id']) ? (int) $item['regiao-imediata']['regiao-intermediaria']['id'] : null,
            'regiaoIntermediariaNome' => isset($item['regiao-imediata']['regiao-intermediaria']['nome']) ? (string) $item['regiao-imediata']['regiao-intermediaria']['nome'] : null,
            'regiaoImediataCodigoIbge' => isset($item['regiao-imediata']['id']) ? (int) $item['regiao-imediata']['id'] : null,
            'regiaoImediataNome' => isset($item['regiao-imediata']['nome']) ? (string) $item['regiao-imediata']['nome'] : null,
            'microrregiaoCodigoIbge' => isset($item['microrregiao']['id']) ? (int) $item['microrregiao']['id'] : null,
            'microrregiaoNome' => isset($item['microrregiao']['nome']) ? (string) $item['microrregiao']['nome'] : null,
            'mesorregiaoCodigoIbge' => isset($item['microrregiao']['mesorregiao']['id']) ? (int) $item['microrregiao']['mesorregiao']['id'] : null,
            'mesorregiaoNome' => isset($item['microrregiao']['mesorregiao']['nome']) ? (string) $item['microrregiao']['mesorregiao']['nome'] : null,
        ];

        $normalized['hashPayload'] = hash('sha256', json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

        return $normalized;
    }
}
