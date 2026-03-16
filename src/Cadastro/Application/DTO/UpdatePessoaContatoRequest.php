<?php

declare(strict_types=1);

namespace App\Cadastro\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdatePessoaContatoRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 160)]
    public string $nomeContato = '';

    #[Assert\Length(max: 80)]
    public ?string $cargo = null;

    #[Assert\Email]
    #[Assert\Length(max: 160)]
    public ?string $email = null;

    #[Assert\Length(max: 30)]
    public ?string $telefone = null;

    public bool $principal = false;
}
