<?php

declare(strict_types=1);

namespace App\Cadastro\Application\Service;

use App\Cadastro\Domain\Entity\Referencia\Pais;
use App\Cadastro\Domain\Repository\PaisRepositoryInterface;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use Doctrine\ORM\EntityManagerInterface;

final class IbgePaisSyncService
{
    private const ENDPOINT = 'https://servicodados.ibge.gov.br/api/v1/localidades/paises';

    public function __construct(
        private readonly PaisRepositoryInterface $repo,
        private readonly TransactionRunnerInterface $tx,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function sync(): array
    {
        $payload = $this->fetch();
        $existing = [];
        foreach ($this->repo->listAll() as $pais) {
            $existing[$pais->getCodigoM49()] = $pais;
        }

        $seen = [];
        $created = 0;
        $updated = 0;
        $unchanged = 0;
        $inactivated = 0;

        $this->tx->run(function () use ($payload, $existing, &$seen, &$created, &$updated, &$unchanged, &$inactivated): void {
            foreach ($payload as $item) {
                $n = $this->normalize($item);
                $codigo = $n['codigoM49'];
                $seen[$codigo] = true;
                $current = $existing[$codigo] ?? null;

                if ($current === null) {
                    $this->entityManager->persist(new Pais(
                        $n['codigoM49'],
                        $n['isoAlpha2'],
                        $n['isoAlpha3'],
                        $n['nome'],
                        $n['regiaoCodigoM49'],
                        $n['regiaoNome'],
                        $n['subRegiaoCodigoM49'],
                        $n['subRegiaoNome'],
                        $n['regiaoIntermediariaCodigoM49'],
                        $n['regiaoIntermediariaNome'],
                        $n['hashPayload'],
                    ));
                    ++$created;
                    continue;
                }

                if ($current->getHashPayload() === $n['hashPayload']) {
                    ++$unchanged;
                    continue;
                }

                $current->updateFromSync(
                    $n['nome'],
                    $n['regiaoCodigoM49'],
                    $n['regiaoNome'],
                    $n['subRegiaoCodigoM49'],
                    $n['subRegiaoNome'],
                    $n['regiaoIntermediariaCodigoM49'],
                    $n['regiaoIntermediariaNome'],
                    $n['hashPayload'],
                );
                ++$updated;
            }

            foreach ($existing as $codigo => $pais) {
                if (!isset($seen[$codigo])) {
                    $pais->markInactive();
                    ++$updated;
                    ++$inactivated;
                }
            }

            $this->entityManager->flush();
        });

        return [
            'total' => \count($payload),
            'created' => $created,
            'updated' => $updated,
            'unchanged' => $unchanged,
            'inactive' => $inactivated,
        ];
    }

    private function fetch(): array
    {
        return $this->fetchJson(self::ENDPOINT);
    }

    private function fetchJson(string $url): array
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
        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            throw new \RuntimeException('Falha ao consultar a API do IBGE.');
        }
        if (str_starts_with($response, "\x1f\x8b")) {
            $decoded = gzdecode($response);
            if ($decoded !== false) {
                $response = $decoded;
            }
        }
        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new \RuntimeException('Resposta inválida da API do IBGE.');
        }
        return $data;
    }

    private function normalize(array $item): array
    {
        $regiao = $item['sub-regiao']['regiao'] ?? null;
        $subRegiao = $item['sub-regiao'] ?? null;
        $regiaoIntermediaria = $item['regiao-intermediaria'] ?? null;
        $normalized = [
            'codigoM49' => (int) $item['id']['M49'],
            'isoAlpha2' => (string) $item['id']['ISO-ALPHA-2'],
            'isoAlpha3' => (string) $item['id']['ISO-ALPHA-3'],
            'nome' => (string) $item['nome'],
            'regiaoCodigoM49' => isset($regiao['id']['M49']) ? (int) $regiao['id']['M49'] : null,
            'regiaoNome' => isset($regiao['nome']) ? (string) $regiao['nome'] : null,
            'subRegiaoCodigoM49' => isset($subRegiao['id']['M49']) ? (int) $subRegiao['id']['M49'] : null,
            'subRegiaoNome' => isset($subRegiao['nome']) ? (string) $subRegiao['nome'] : null,
            'regiaoIntermediariaCodigoM49' => isset($regiaoIntermediaria['id']['M49']) ? (int) $regiaoIntermediaria['id']['M49'] : null,
            'regiaoIntermediariaNome' => isset($regiaoIntermediaria['nome']) ? (string) $regiaoIntermediaria['nome'] : null,
        ];
        $normalized['hashPayload'] = hash('sha256', json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        return $normalized;
    }
}
