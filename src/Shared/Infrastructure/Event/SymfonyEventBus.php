<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Event;

use App\Shared\Application\Bus\EventBusInterface;
use App\Shared\Domain\Event\DomainEventInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SymfonyEventBus implements EventBusInterface
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function publish(DomainEventInterface $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }
}
