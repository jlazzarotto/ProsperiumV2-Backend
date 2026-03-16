<?php

declare(strict_types=1);

namespace App\Cadastro\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreatePessoaEnderecoRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $pessoaId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    public string $tipoEndereco = 'Comercial';

    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    public string $logradouro = '';

    #[Assert\Length(max: 20)]
    public ?string $numero = null;

    #[Assert\Length(max: 120)]
    public ?string $complemento = null;

    #[Assert\Length(max: 120)]
    public ?string $bairro = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    public string $cidade = '';

    #[Assert\Length(max: 2)]
    public ?string $uf = null;

    #[Assert\Length(max: 10)]
    public ?string $cep = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 60)]
    public string $pais = 'Brasil';

    public bool $principal = false;
}
