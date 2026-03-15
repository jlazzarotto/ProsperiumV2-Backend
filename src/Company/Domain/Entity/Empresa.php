<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Company\Infrastructure\Persistence\Doctrine\DoctrineEmpresaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineEmpresaRepository::class)]
#[ORM\Table(name: 'empresas')]
#[ORM\UniqueConstraint(name: 'uk_empresas_company_cnpj', columns: ['company_id', 'cnpj'])]
class Empresa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Company $company;

    #[ORM\Column(name: 'razao_social', length: 255)]
    private string $razaoSocial;

    #[ORM\Column(name: 'nome_fantasia', length: 255, nullable: true)]
    private ?string $nomeFantasia;

    #[ORM\Column(length: 20)]
    private string $cnpj;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Company $company, string $razaoSocial, ?string $nomeFantasia, string $cnpj, string $status = 'active')
    {
        $now = new \DateTimeImmutable();
        $this->company = $company;
        $this->razaoSocial = trim($razaoSocial);
        $this->nomeFantasia = $nomeFantasia !== null ? trim($nomeFantasia) : null;
        $this->cnpj = $cnpj;
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

    public function getRazaoSocial(): string
    {
        return $this->razaoSocial;
    }

    public function getNomeFantasia(): ?string
    {
        return $this->nomeFantasia;
    }

    public function getCnpj(): string
    {
        return $this->cnpj;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function update(Company $company, string $razaoSocial, ?string $nomeFantasia, string $cnpj, string $status): void
    {
        $this->company = $company;
        $this->razaoSocial = trim($razaoSocial);
        $this->nomeFantasia = $nomeFantasia !== null ? trim($nomeFantasia) : null;
        $this->cnpj = $cnpj;
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
