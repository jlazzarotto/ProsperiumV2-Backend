<?php

declare(strict_types=1);

namespace App\Financeiro\Domain\Event;

use App\Shared\Domain\Event\DomainEventInterface;

final class TituloBaixado implements DomainEventInterface
{
    public function __construct(public readonly int $tituloId, public readonly int $parcelaId, public readonly int $companyId, private readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable()) {}
    public function occurredAt(): \DateTimeImmutable { return $this->occurredAt; }
}
