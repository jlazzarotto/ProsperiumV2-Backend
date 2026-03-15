<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Event;
use App\Shared\Domain\Event\DomainEventInterface;
final class AprovacaoTituloSolicitada implements DomainEventInterface
{
    public function __construct(public readonly int $aprovacaoId, public readonly int $companyId, private readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable()) {}
    public function occurredAt(): \DateTimeImmutable { return $this->occurredAt; }
}
