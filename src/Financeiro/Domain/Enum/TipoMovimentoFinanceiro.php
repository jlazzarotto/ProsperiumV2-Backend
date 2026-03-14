<?php

declare(strict_types=1);

namespace App\Financeiro\Domain\Enum;

enum TipoMovimentoFinanceiro: string
{
    case ATIVO = 'ativo';
}
