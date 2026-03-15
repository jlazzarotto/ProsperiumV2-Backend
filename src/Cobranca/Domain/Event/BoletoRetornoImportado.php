<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Event;
use App\Shared\Domain\Event\DomainEventInterface;
final class BoletoRetornoImportado implements DomainEventInterface
{
    public function __construct(public readonly int $companyId, public readonly int $itensImportados, private readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable()) {}
    public function occurredAt(): \DateTimeImmutable { return $this->occurredAt; }
}
