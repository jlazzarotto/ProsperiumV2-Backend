<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity\Tenant;

use App\Company\Infrastructure\Persistence\Doctrine\DoctrineUnidadeNegocioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineUnidadeNegocioRepository::class)]
#[ORM\Table(name: 'unidades_negocio')]
#[ORM\UniqueConstraint(name: 'uk_unidades_negocio_company_nome', columns: ['company_id', 'nome'])]
#[ORM\UniqueConstraint(name: 'uk_unidades_negocio_company_abrev', columns: ['company_id', 'abreviatura'])]
class UnidadeNegocio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])]
    private int $companyId;

    #[ORM\Column(length: 255)]
    private string $nome;

    #[ORM\Column(length: 50)]
    private string $abreviatura;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(int $companyId, string $nome, string $abreviatura, string $status = 'active')
    {
        $now = new \DateTimeImmutable();
        $this->companyId = $companyId;
        $this->nome = trim($nome);
        $this->abreviatura = trim($abreviatura);
        $this->status = $status;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getAbreviatura(): string
    {
        return $this->abreviatura;
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

    public function setCompanyId(int $companyId): void
    {
        $this->companyId = $companyId;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setNome(string $nome): void
    {
        $this->nome = trim($nome);
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setAbreviatura(string $abreviatura): void
    {
        $this->abreviatura = trim($abreviatura);
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
