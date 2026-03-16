<?php

declare(strict_types=1);

namespace App\Cadastro\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdatePessoaRequest
{
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['PF', 'PJ'])]
    public string $tipoPessoa = 'PF';

    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    public string $nomeRazao = '';

    #[Assert\Length(max: 180)]
    public ?string $nomeFantasia = null;

    #[Assert\Length(max: 20)]
    public ?string $documento = null;

    #[Assert\Length(max: 30)]
    public ?string $inscricaoEstadual = null;

    #[Assert\Email]
    #[Assert\Length(max: 160)]
    public ?string $emailPrincipal = null;

    #[Assert\Length(max: 30)]
    public ?string $telefonePrincipal = null;

    #[Assert\Length(max: 3)]
    #[Assert\Regex(pattern: '/^[CFU]{0,3}$/', message: 'Classificacao deve conter apenas C, F, U.')]
    public ?string $classificacao = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['active', 'inactive'])]
    public string $status = 'active';
}
