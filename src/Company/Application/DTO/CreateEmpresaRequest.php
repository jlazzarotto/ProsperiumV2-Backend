<?php

declare(strict_types=1);

namespace App\Company\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateEmpresaRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $companyId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $razaoSocial = '';

    #[Assert\Length(max: 255)]
    public ?string $nomeFantasia = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    public string $cnpj = '';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['active', 'inactive'])]
    public string $status = 'active';
}
