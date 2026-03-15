<?php

declare(strict_types=1);

namespace App\Company\Domain\Service;

use App\Shared\Domain\Exception\ValidationException;

final class CnpjNormalizer
{
    public function normalize(string $cnpj): string
    {
        $digits = preg_replace('/\D+/', '', $cnpj) ?? '';

        if (strlen($digits) !== 14) {
            throw new ValidationException([
                'cnpj' => ['CNPJ deve conter 14 dígitos.'],
            ]);
        }

        return $digits;
    }
}
