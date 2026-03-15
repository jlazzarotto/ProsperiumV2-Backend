<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Contract\TransactionRunnerInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTransactionRunner implements TransactionRunnerInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function run(callable $operation): mixed
    {
        return $this->entityManager->wrapInTransaction(
            static fn (): mixed => $operation()
        );
    }
}
