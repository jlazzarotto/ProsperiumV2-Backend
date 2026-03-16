<?php

declare(strict_types=1);

namespace App\Company\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateEmpresaRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $companyId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $razaoSocial = '';

    #[Assert\Length(max: 255)]
    public ?string $nomeFantasia = null;

    #[Assert\Length(max: 255)]
    public ?string $apelido = null;

    #[Assert\Length(max: 50)]
    public ?string $abreviatura = null;

    #[Assert\Length(max: 20)]
    public ?string $cnpj = null;

    #[Assert\Length(max: 50)]
    public ?string $inscricaoEstadual = null;

    #[Assert\Length(max: 50)]
    public ?string $inscricaoMunicipal = null;

    #[Assert\Length(max: 9)]
    public ?string $cep = null;

    #[Assert\Length(max: 50)]
    public ?string $estado = null;

    #[Assert\Length(max: 100)]
    public ?string $cidade = null;

    #[Assert\Length(max: 255)]
    public ?string $logradouro = null;

    #[Assert\Length(max: 20)]
    public ?string $numero = null;

    #[Assert\Length(max: 100)]
    public ?string $complemento = null;

    #[Assert\Length(max: 100)]
    public ?string $bairro = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['active', 'inactive'])]
    public string $status = 'active';
}
