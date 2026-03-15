<?php

declare(strict_types=1);

namespace App\Tesouraria\Domain\Service;

use App\Financeiro\Domain\Entity\MovimentoFinanceiro;
use App\Tesouraria\Domain\Entity\ConciliacaoRegra;
use App\Tesouraria\Domain\Entity\ExtratoBancario;

final class SugestaoConciliacaoService
{
    /**
     * @param list<ConciliacaoRegra> $regras
     * @param list<MovimentoFinanceiro> $movimentos
     * @return array{regraId:int|null,movimentoId:int|null,score:int}
     */
    public function sugerir(ExtratoBancario $extrato, array $regras, array $movimentos): array
    {
        $descricao = mb_strtolower($extrato->getDescricao());

        foreach ($regras as $regra) {
            if ($regra->getContaFinanceira() !== null && $regra->getContaFinanceira()->getId() !== $extrato->getContaFinanceira()->getId()) {
                continue;
            }

            if (str_contains($descricao, $regra->getDescricaoContains())) {
                foreach ($movimentos as $movimento) {
                    if ($movimento->getContaFinanceira()->getId() === $extrato->getContaFinanceira()->getId() && abs((float)$movimento->getValor() - (float)$extrato->getValor()) < 0.009) {
                        return ['regraId' => (int) $regra->getId(), 'movimentoId' => (int) $movimento->getId(), 'score' => 100];
                    }
                }
            }
        }

        foreach ($movimentos as $movimento) {
            if ($movimento->getContaFinanceira()->getId() === $extrato->getContaFinanceira()->getId() && abs((float)$movimento->getValor() - (float)$extrato->getValor()) < 0.009) {
                return ['regraId' => null, 'movimentoId' => (int) $movimento->getId(), 'score' => 70];
            }
        }

        return ['regraId' => null, 'movimentoId' => null, 'score' => 0];
    }
}
