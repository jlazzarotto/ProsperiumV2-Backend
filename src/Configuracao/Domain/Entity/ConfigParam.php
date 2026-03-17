<?php

declare(strict_types=1);

namespace App\Configuracao\Domain\Entity;

use App\Configuracao\Infrastructure\Persistence\Doctrine\DoctrineConfigParamRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineConfigParamRepository::class)]
#[ORM\Table(name: 'config_params')]
#[ORM\Index(name: 'idx_config_params_company_status', columns: ['company_id', 'status'])]
#[ORM\UniqueConstraint(name: 'uk_config_params_company_name', columns: ['company_id', 'name'])]
class ConfigParam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])]
    private int $companyId;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(name: '`type`', length: 100, nullable: true)]
    private ?string $type;

    #[ORM\Column(type: 'text')]
    private string $value;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'smallint', options: ['default' => 1])]
    private int $status;

    #[ORM\Column(name: '`restrict`', type: 'smallint', options: ['default' => 1])]
    private int $restrict;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        int $companyId,
        string $name,
        string $value,
        ?string $type = null,
        ?string $description = null,
        int $status = 1,
        int $restrict = 1,
    ) {
        $now = new \DateTimeImmutable();
        $this->companyId = $companyId;
        $this->name = trim($name);
        $this->value = trim($value);
        $this->type = $type !== null ? trim($type) : null;
        $this->description = $description !== null ? trim($description) : null;
        $this->status = $status;
        $this->restrict = $restrict;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getName(): string { return $this->name; }
    public function getType(): ?string { return $this->type; }
    public function getValue(): string { return $this->value; }
    public function getDescription(): ?string { return $this->description; }
    public function getStatus(): int { return $this->status; }
    public function getRestrict(): int { return $this->restrict; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function update(string $name, string $value, ?string $type, ?string $description): void
    {
        $this->name = trim($name);
        $this->value = trim($value);
        $this->type = $type !== null ? trim($type) : null;
        $this->description = $description !== null ? trim($description) : null;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateStatus(int $status): void
    {
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateRestrict(int $restrict): void
    {
        $this->restrict = $restrict;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
