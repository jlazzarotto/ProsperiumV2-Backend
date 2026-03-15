<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Company\Infrastructure\Persistence\Doctrine\DoctrineCompanyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineCompanyRepository::class)]
#[ORM\Table(name: 'companies')]
#[ORM\UniqueConstraint(name: 'uk_companies_nome', columns: ['nome'])]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $nome;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(string $nome, string $status = 'active')
    {
        $now = new \DateTimeImmutable();
        $this->nome = trim($nome);
        $this->status = $status;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function update(string $nome, string $status): void
    {
        $this->nome = trim($nome);
        $this->status = $status;
        $this->touch();
    }
}
