<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use Throwable;

class DomainException extends \RuntimeException
{
    public function __construct(string $message = 'Domain error.', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
