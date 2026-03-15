<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Event;
use App\Shared\Domain\Event\DomainEventInterface;
final class TarefaBpoCriada implements DomainEventInterface
{
    public function __construct(public readonly int $tarefaId, public readonly int $companyId, private readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable()) {}
    public function occurredAt(): \DateTimeImmutable { return $this->occurredAt; }
}
