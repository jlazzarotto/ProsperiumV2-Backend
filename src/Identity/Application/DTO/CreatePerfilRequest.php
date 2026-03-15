<?php

declare(strict_types=1);

namespace App\Identity\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreatePerfilRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $companyId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public string $codigo = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $nome = '';

    /** @var list<string> */
    #[Assert\NotBlank]
    #[Assert\Count(min: 1)]
    #[Assert\All([new Assert\NotBlank(), new Assert\Length(max: 120)])]
    public array $permissionCodes = [];

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['custom'])]
    public string $tipo = 'custom';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['active', 'inactive'])]
    public string $status = 'active';
}
