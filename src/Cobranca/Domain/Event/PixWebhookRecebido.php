<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Event;
use App\Shared\Domain\Event\DomainEventInterface;
final class PixWebhookRecebido implements DomainEventInterface
{
    public function __construct(public readonly int $companyId, public readonly string $tipoEvento, private readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable()) {}
    public function occurredAt(): \DateTimeImmutable { return $this->occurredAt; }
}
