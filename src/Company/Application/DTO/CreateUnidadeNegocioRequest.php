<?php

declare(strict_types=1);

namespace App\Company\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateUnidadeNegocioRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $companyId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $nome = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public string $abreviatura = '';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['active', 'inactive'])]
    public string $status = 'active';
}
