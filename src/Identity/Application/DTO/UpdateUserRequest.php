<?php

declare(strict_types=1);

namespace App\Identity\Application\DTO;

use App\Identity\Domain\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateUserRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $nome = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    public string $email = '';

    #[Assert\Length(min: 8, max: 255)]
    public ?string $password = null;

    #[Assert\Positive]
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

    #[Assert\NotBlank]
    #[Assert\Choice(choices: [User::ROLE_ROOT, User::ROLE_ADMIN])]
    public string $role = User::ROLE_ADMIN;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: [User::STATUS_ATIVO, User::STATUS_BLOQUEADO, User::STATUS_INATIVO])]
    public string $status = User::STATUS_ATIVO;

    public bool $mfaHabilitado = false;
}
