<?php

declare(strict_types=1);

namespace App\Identity\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdatePerfilRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $nome = '';

    /** @var list<string> */
    #[Assert\All([new Assert\NotBlank(), new Assert\Length(max: 120)])]
    public array $permissionCodes = [];

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['custom', 'system'])]
    public string $tipo = 'custom';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['active', 'inactive'])]
    public string $status = 'active';
}
