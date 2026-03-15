<?php

declare(strict_types=1);

namespace App\Integracao\Psp\Domain\Entity;

use App\Integracao\Psp\Infrastructure\Persistence\Doctrine\DoctrinePspConsultaHistoricoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrinePspConsultaHistoricoRepository::class)]
#[ORM\Table(name: 'psp_consultas_historico')]
#[ORM\Index(name: 'idx_psp_consultas_lookup', columns: ['endpoint_key', 'created_at'])]
#[ORM\Index(name: 'idx_psp_consultas_company', columns: ['company_id', 'created_at'])]
final class PspConsultaHistorico
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'company_id', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $companyId;

    #[ORM\Column(name: 'user_id', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $userId;

    #[ORM\Column(name: 'endpoint_key', length: 80)]
    private string $endpointKey;

    #[ORM\Column(name: 'request_json', type: 'json')]
    private array $requestJson;

    #[ORM\Column(name: 'response_json', type: 'json', nullable: true)]
    private ?array $responseJson;

    #[ORM\Column(type: 'boolean')]
    private bool $success;

    #[ORM\Column(name: 'duration_ms', type: 'integer')]
    private int $durationMs;

    #[ORM\Column(name: 'error_message', length: 500, nullable: true)]
    private ?string $errorMessage;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * @param array<string, mixed> $requestJson
     * @param array<string, mixed>|null $responseJson
     */
    public function __construct(
        ?int $companyId,
        ?int $userId,
        string $endpointKey,
        array $requestJson,
        ?array $responseJson,
        bool $success,
        int $durationMs,
        ?string $errorMessage = null,
    ) {
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->endpointKey = trim($endpointKey);
        $this->requestJson = $requestJson;
        $this->responseJson = $responseJson;
        $this->success = $success;
        $this->durationMs = $durationMs;
        $this->errorMessage = $errorMessage !== null ? trim($errorMessage) : null;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): ?int { return $this->companyId; }
    public function getUserId(): ?int { return $this->userId; }
    public function getEndpointKey(): string { return $this->endpointKey; }
    /** @return array<string, mixed> */
    public function getRequestJson(): array { return $this->requestJson; }
    /** @return array<string, mixed>|null */
    public function getResponseJson(): ?array { return $this->responseJson; }
    public function isSuccess(): bool { return $this->success; }
    public function getDurationMs(): int { return $this->durationMs; }
    public function getErrorMessage(): ?string { return $this->errorMessage; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
