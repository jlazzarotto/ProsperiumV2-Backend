<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

final class Slugger
{
    public function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';

        return trim($value, '-');
    }
}
