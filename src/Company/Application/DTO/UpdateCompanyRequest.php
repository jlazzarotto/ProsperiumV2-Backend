<?php

declare(strict_types=1);

namespace App\Company\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateCompanyRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $nome = '';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['shared', 'dedicated'])]
    public string $tenancyMode = 'shared';

    #[Assert\Length(max: 100)]
    public ?string $databaseKey = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['active', 'inactive'])]
    public string $status = 'active';
}
