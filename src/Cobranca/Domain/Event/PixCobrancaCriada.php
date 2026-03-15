<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Event;
use App\Shared\Domain\Event\DomainEventInterface;
final class PixCobrancaCriada implements DomainEventInterface
{
    public function __construct(public readonly int $pixCobrancaId, public readonly int $companyId, private readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable()) {}
    public function occurredAt(): \DateTimeImmutable { return $this->occurredAt; }
}
