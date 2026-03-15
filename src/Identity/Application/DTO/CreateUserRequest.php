<?php

declare(strict_types=1);

namespace App\Identity\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $nome = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    public string $email = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 255)]
    public string $password = '';

    public ?int $companyId = null;

    /** @var list<int> */
    #[Assert\All([new Assert\Positive()])]
    public array $empresaIds = [];

    /** @var list<int> */
    #[Assert\All([new Assert\Positive()])]
    public array $unidadeIds = [];

    /** @var list<string> */
    #[Assert\All([new Assert\NotBlank(), new Assert\Length(max: 100)])]
    public array $profileCodes = [];

    public bool $isCompanyAdmin = false;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: [\App\Identity\Domain\Entity\User::ROLE_ROOT, \App\Identity\Domain\Entity\User::ROLE_ADMIN])]
    public string $role = \App\Identity\Domain\Entity\User::ROLE_ADMIN;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['active', 'inactive'])]
    public string $status = 'active';
}
