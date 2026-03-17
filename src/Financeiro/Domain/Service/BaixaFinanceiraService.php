<?php

declare(strict_types=1);

namespace App\Financeiro\Domain\Service;

use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Financeiro\Domain\Entity\Baixa;
use App\Financeiro\Domain\Entity\MovimentoFinanceiro;
use App\Financeiro\Domain\Entity\TituloParcela;
use App\Shared\Domain\Exception\ValidationException;

final class BaixaFinanceiraService
{
    /**
     * @return array{baixa: Baixa, movimento: MovimentoFinanceiro}
     */
    public function baixar(TituloParcela $parcela, ContaFinanceira $contaFinanceira, string $valor, \DateTimeImmutable $dataPagamento, ?string $observacoes): array
    {
        $valorFloat = (float) $valor;
        $aberto = (float) $parcela->getValorAberto();

        if ($valorFloat <= 0) {
            throw new ValidationException(['valor' => ['O valor da baixa deve ser positivo.']]);
        }

        if ($valorFloat - $aberto > 0.009) {
            throw new ValidationException(['valor' => ['O valor da baixa não pode ser maior que o saldo aberto da parcela.']]);
        }

        $parcela->baixar(number_format($valorFloat, 2, '.', ''));

        $baixa = new Baixa(
            $parcela->getCompanyId(),
            $parcela->getEmpresa(),
            $parcela->getUnidade(),
            $parcela,
            $contaFinanceira,
            number_format($valorFloat, 2, '.', ''),
            $dataPagamento,
            $observacoes
        );

        $movimento = new MovimentoFinanceiro(
            $parcela->getCompanyId(),
            $parcela->getEmpresa(),
            $parcela->getUnidade(),
            $contaFinanceira,
            $parcela->getTitulo(),
            $baixa,
            $parcela->getTitulo()->getTipo() === 'receber' ? 'credito' : 'debito',
            number_format($valorFloat, 2, '.', ''),
            $dataPagamento,
            $observacoes
        );

        return ['baixa' => $baixa, 'movimento' => $movimento];
    }
}
