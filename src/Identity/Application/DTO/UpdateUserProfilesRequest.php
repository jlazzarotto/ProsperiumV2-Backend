<?php

declare(strict_types=1);

namespace App\Identity\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateUserProfilesRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $companyId = null;

    /** @var list<string> */
    #[Assert\All([new Assert\NotBlank(), new Assert\Length(max: 100)])]
    public array $profileCodes = [];
}
