<?php

declare(strict_types=1);

namespace App\Configuracao\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class SaveConfigParamRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $companyId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name = '';

    #[Assert\NotBlank]
    public string $value = '';

    #[Assert\Length(max: 100)]
    public ?string $type = null;

    public ?string $description = null;

    public ?string $originalName = null;
}
