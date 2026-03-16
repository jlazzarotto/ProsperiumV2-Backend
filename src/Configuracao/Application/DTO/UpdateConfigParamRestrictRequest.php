<?php

declare(strict_types=1);

namespace App\Configuracao\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateConfigParamRestrictRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $companyId = null;

    #[Assert\NotBlank]
    public string $name = '';

    #[Assert\NotNull]
    #[Assert\Choice(choices: [1, 2])]
    public ?int $restrict = null;
}
