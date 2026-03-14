<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTransactionManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function transactional(callable $callback): mixed
    {
        return $this->entityManager->wrapInTransaction($callback);
    }
}
