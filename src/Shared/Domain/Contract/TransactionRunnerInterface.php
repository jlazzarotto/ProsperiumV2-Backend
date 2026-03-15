<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contract;

interface TransactionRunnerInterface
{
    public function run(callable $operation): mixed;
}
