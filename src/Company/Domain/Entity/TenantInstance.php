<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Company\Infrastructure\Persistence\Doctrine\DoctrineTenantInstanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineTenantInstanceRepository::class)]
#[ORM\Table(name: 'tenant_instances')]
#[ORM\UniqueConstraint(name: 'uk_tenant_instances_company', columns: ['company_id'])]
class TenantInstance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Company $company;

    #[ORM\Column(name: 'tenancy_mode', length: 20)]
    private string $tenancyMode;

    #[ORM\Column(name: 'database_key', length: 100)]
    private string $databaseKey;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Company $company, string $tenancyMode, string $databaseKey, string $status = 'active')
    {
        $now = new \DateTimeImmutable();
        $this->company = $company;
        $this->tenancyMode = $tenancyMode;
        $this->databaseKey = trim($databaseKey);
        $this->status = $status;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getTenancyMode(): string
    {
        return $this->tenancyMode;
    }

    public function getDatabaseKey(): string
    {
        return $this->databaseKey;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function update(string $tenancyMode, string $databaseKey, string $status): void
    {
        $this->tenancyMode = $tenancyMode;
        $this->databaseKey = trim($databaseKey);
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
