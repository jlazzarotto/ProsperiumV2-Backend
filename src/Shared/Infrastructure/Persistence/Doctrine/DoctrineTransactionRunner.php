<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Infrastructure\MultiTenancy\TenantContext;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTransactionRunner implements TransactionRunnerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $controlEntityManager,
        private readonly EntityManagerInterface $tenantEntityManager,
        private readonly TenantContext $tenantContext
    )
    {
    }

    public function run(callable $operation): mixed
    {
        $entityManager = $this->tenantContext->getResolvedDatabaseUrl() !== null
            ? $this->tenantEntityManager
            : $this->controlEntityManager;

        return $entityManager->wrapInTransaction(
            static fn (): mixed => $operation()
        );
    }
}
