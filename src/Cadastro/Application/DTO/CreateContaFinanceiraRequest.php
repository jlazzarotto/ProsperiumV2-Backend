<?php

declare(strict_types=1);

namespace App\Cadastro\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateContaFinanceiraRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $companyId = null;

    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $empresaId = null;

    #[Assert\Positive]
    public ?int $unidadeId = null;

    #[Assert\Positive]
    public ?int $bancoId = null;

    #[Assert\Positive]
    public ?int $titularPessoaId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public string $codigo = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $nome = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public string $tipo = 'caixa';

    #[Assert\Length(max: 20)]
    public ?string $agencia = null;

    #[Assert\Length(max: 30)]
    public ?string $contaNumero = null;

    #[Assert\Length(max: 5)]
    public ?string $contaDigito = null;

    #[Assert\PositiveOrZero]
    public float $saldoInicial = 0.0;

    #[Assert\Date]
    public ?string $dataSaldoInicial = null;

    public bool $permiteMovimentoNegativo = false;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['active', 'inactive'])]
    public string $status = 'active';
}
