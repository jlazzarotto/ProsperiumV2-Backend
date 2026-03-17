<?php

declare(strict_types=1);

namespace App\Financeiro\Domain\Service;

use App\Financeiro\Domain\Entity\Titulo;
use App\Financeiro\Domain\Entity\TituloParcela;
use App\Shared\Domain\Exception\ValidationException;

final class ParcelamentoService
{
    /**
     * @param list<array{numero:int,valor:string,vencimento:\DateTimeImmutable}> $parcelasData
     * @return list<TituloParcela>
     */
    public function gerarParcelas(Titulo $titulo, array $parcelasData): array
    {
        if ($parcelasData === []) {
            throw new ValidationException(['parcelas' => ['Informe ao menos uma parcela.']]);
        }

        $soma = 0.0;
        $parcelas = [];

        foreach ($parcelasData as $parcelaData) {
            $valor = (float) $parcelaData['valor'];

            if ($valor <= 0) {
                throw new ValidationException(['parcelas' => ['Valores de parcelas devem ser positivos.']]);
            }

            $soma += $valor;
            $parcelas[] = new TituloParcela(
                $titulo,
                $titulo->getCompanyId(),
                $titulo->getEmpresa(),
                $titulo->getUnidade(),
                $parcelaData['numero'],
                number_format($valor, 2, '.', ''),
                $parcelaData['vencimento']
            );
        }

        if (abs($soma - (float) $titulo->getValorTotal()) > 0.009) {
            throw new ValidationException(['parcelas' => ['A soma das parcelas deve ser igual ao valor total do título.']]);
        }

        return $parcelas;
    }
}
