<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus;

use App\Shared\Domain\Event\DomainEventInterface;

interface EventBusInterface
{
    public function publish(DomainEventInterface $event): void;
}
