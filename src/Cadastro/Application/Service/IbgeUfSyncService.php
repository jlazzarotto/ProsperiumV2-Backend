<?php

declare(strict_types=1);

namespace App\Cadastro\Application\Service;

use App\Cadastro\Domain\Entity\Referencia\Uf;
use App\Cadastro\Domain\Repository\UfRepositoryInterface;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use Doctrine\ORM\EntityManagerInterface;

final class IbgeUfSyncService
{
    private const ENDPOINT = 'https://servicodados.ibge.gov.br/api/v1/localidades/estados';

    public function __construct(
        private readonly UfRepositoryInterface $repo,
        private readonly TransactionRunnerInterface $tx,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function sync(): array
    {
        $payload = $this->fetchJson(self::ENDPOINT);
        $existing = [];
        foreach ($this->repo->listAll() as $uf) {
            $existing[$uf->getCodigoIbge()] = $uf;
        }

        $seen = [];
        $created = 0;
        $updated = 0;
        $unchanged = 0;
        $inactivated = 0;

        $this->tx->run(function () use ($payload, $existing, &$seen, &$created, &$updated, &$unchanged, &$inactivated): void {
            foreach ($payload as $item) {
                $n = $this->normalize($item);
                $codigo = $n['codigoIbge'];
                $seen[$codigo] = true;
                $current = $existing[$codigo] ?? null;

                if ($current === null) {
                    $this->entityManager->persist(new Uf(
                        $n['codigoIbge'],
                        $n['sigla'],
                        $n['nome'],
                        $n['regiaoCodigoIbge'],
                        $n['regiaoSigla'],
                        $n['regiaoNome'],
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
                    $n['regiaoCodigoIbge'],
                    $n['regiaoSigla'],
                    $n['regiaoNome'],
                    $n['hashPayload'],
                );
                ++$updated;
            }

            foreach ($existing as $codigo => $uf) {
                if (!isset($seen[$codigo])) {
                    $uf->markInactive();
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
        $normalized = [
            'codigoIbge' => (int) $item['id'],
            'sigla' => (string) $item['sigla'],
            'nome' => (string) $item['nome'],
            'regiaoCodigoIbge' => (int) $item['regiao']['id'],
            'regiaoSigla' => (string) $item['regiao']['sigla'],
            'regiaoNome' => (string) $item['regiao']['nome'],
        ];
        $normalized['hashPayload'] = hash('sha256', json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        return $normalized;
    }
}
