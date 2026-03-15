<?php

declare(strict_types=1);

namespace App\Shared\Application\Validation;

use App\Shared\Domain\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestValidator
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validate(object $request): void
    {
        $violations = $this->validator->validate($request);

        if (count($violations) === 0) {
            return;
        }

        $errors = [];

        foreach ($violations as $violation) {
            $property = $violation->getPropertyPath();
            $errors[$property][] = $violation->getMessage();
        }

        throw new ValidationException($errors);
    }
}
