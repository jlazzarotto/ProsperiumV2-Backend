<?php

declare(strict_types=1);

namespace App\Integracao\Psp\Application\DTO;

final class PspResponses
{
    /**
     * @param list<array<string, mixed>> $items
     *
     * @return list<array<string, mixed>>
     */
    public static function duvs(array $items): array
    {
        return array_map([self::class, 'duv'], $items);
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public static function duv(array $item): array
    {
        return [
            'numeroDuv' => self::scalar($item['numeroDuv'] ?? null),
            'nomeEmbarcacao' => self::string($item['nomeEmbarcacao'] ?? $item['nome'] ?? $item['embarcacaoNome'] ?? null),
            'porto' => self::string($item['porto'] ?? $item['nomePorto'] ?? null),
            'bitrigramaPorto' => self::string($item['bitrigramaPorto'] ?? null),
            'natureza' => self::string($item['natureza'] ?? $item['tipoEstadia'] ?? $item['nomeCorrenteTrafego'] ?? null),
            'situacaoDuv' => self::string($item['situacaoDuv'] ?? $item['situacao'] ?? $item['status'] ?? null),
            'finalizado' => self::bool($item['finalizado'] ?? null),
        ];
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public static function resumo(array $item): array
    {
        return [
            'numeroDuv' => self::scalar($item['numeroDuv'] ?? null),
            'nomeEmbarcacao' => self::string($item['nomeEmbarcacao'] ?? $item['nome'] ?? $item['embarcacaoNome'] ?? null),
            'porto' => self::string($item['porto'] ?? $item['nomePorto'] ?? null),
            'bitrigramaPorto' => self::string($item['bitrigramaPorto'] ?? null),
            'natureza' => self::string($item['natureza'] ?? $item['tipoEstadia'] ?? $item['nomeCorrenteTrafego'] ?? null),
            'situacaoDuv' => self::string($item['situacaoDuv'] ?? $item['situacao'] ?? $item['status'] ?? null),
            'finalizado' => self::bool($item['finalizado'] ?? null),
        ];
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public static function embarcacao(array $item): array
    {
        return [
            'nome' => self::string($item['nome'] ?? null),
            'imo' => self::string($item['imo'] ?? null),
            'numeroInscricao' => self::string($item['numeroInscricao'] ?? null),
            'bandeira' => self::string($item['bandeira'] ?? null),
            'areaNavegacao' => self::string($item['areaNavegacao'] ?? null),
            'tipoEmbarcacao' => self::string($item['tipoEmbarcacao'] ?? $item['tipo'] ?? null),
            'arqueacaoBruta' => self::scalar($item['arqueacaoBruta'] ?? null),
            'comprimento' => self::scalar($item['comprimento'] ?? null),
        ];
    }

    /**
     * @param list<array<string, mixed>> $items
     *
     * @return list<array<string, mixed>>
     */
    public static function anuencias(array $items): array
    {
        return array_map(static fn (array $item): array => [
            'nomeOrgao' => self::string($item['nomeOrgao'] ?? $item['orgao'] ?? $item['autoridade'] ?? null),
            'situacao' => self::string($item['situacao'] ?? $item['status'] ?? $item['resultado'] ?? null),
            'exigencia' => self::string($item['exigencia'] ?? null),
            'observacao' => self::string($item['observacao'] ?? null),
            'tipo' => self::string($item['tipo'] ?? null),
        ], $items);
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public static function chegadasSaidas(array $item): array
    {
        $eventos = [];
        foreach (self::listOfArrays($item['eventosEstadia'] ?? null) as $evento) {
            $chegada = self::assoc($evento['chegada'] ?? null);
            $saida = self::assoc($evento['saida'] ?? null);
            $eventos[] = [
                'idPSPChegada' => self::scalar($evento['idPSPChegada'] ?? null),
                'codigoEventoMovimentacao' => self::string($evento['codigoEventoMovimentacao'] ?? null),
                'eventoMovimentacao' => self::string($evento['eventoMovimentacao'] ?? null),
                'situacao' => self::string($evento['situacao'] ?? $evento['status'] ?? null),
                'chegada' => [
                    'dataChegada' => self::string($chegada['dataChegada'] ?? $chegada['dataHoraChegada'] ?? $chegada['data'] ?? null),
                    'nomeLocal' => self::string($chegada['nomeLocal'] ?? $chegada['local'] ?? null),
                    'tipoLocal' => self::string($chegada['tipoLocal'] ?? null),
                ],
                'saida' => [
                    'dataSaida' => self::string($saida['dataSaida'] ?? $saida['dataHoraSaida'] ?? $saida['data'] ?? null),
                ],
            ];
        }

        return ['eventosEstadia' => $eventos];
    }

    /**
     * @param list<array<string, mixed>> $items
     *
     * @return list<array<string, mixed>>
     */
    public static function anexos(array $items): array
    {
        return array_map(static fn (array $item): array => [
            'id' => self::scalar($item['id'] ?? null),
            'tipoDocumento' => self::string($item['tipoDocumento'] ?? $item['descricao'] ?? $item['nomeTipoDocumento'] ?? null),
            'nomeArquivo' => self::string($item['nomeArquivo'] ?? $item['arquivo'] ?? null),
            'url' => self::string($item['url'] ?? null),
            'observacoes' => self::string($item['observacoes'] ?? $item['observacao'] ?? null),
        ], $items);
    }

    /**
     * @param list<array<string, mixed>> $items
     *
     * @return list<array<string, mixed>>
     */
    public static function locaisAtracacao(array $items): array
    {
        return array_map(static fn (array $item): array => [
            'idPSPAreaPorto' => self::int($item['idPSPAreaPorto'] ?? null),
            'idPSPBerco' => self::int($item['idPSPBerco'] ?? null),
            'idPSPCabeco' => self::int($item['idPSPCabeco'] ?? null),
            'idPSPFundeadouro' => self::int($item['idPSPFundeadouro'] ?? null),
            'idPSPBoiaAmarracao' => self::int($item['idPSPBoiaAmarracao'] ?? null),
            'nome' => self::string($item['nome'] ?? $item['descricao'] ?? null),
            'descricao' => self::string($item['descricao'] ?? null),
            'tipoLocal' => self::string($item['tipoLocal'] ?? null),
        ], $items);
    }

    private static function assoc(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function listOfArrays(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, static fn (mixed $item): bool => is_array($item)));
    }

    private static function string(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? $value : null;
    }

    private static function int(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private static function bool(mixed $value): ?bool
    {
        return is_bool($value) ? $value : null;
    }

    private static function scalar(mixed $value): string|int|float|bool|null
    {
        return is_scalar($value) ? $value : null;
    }
}
