<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

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

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Company $company;

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

    public function __construct(Company $company, string $nome, string $abreviatura, string $status = 'active')
    {
        $now = new \DateTimeImmutable();
        $this->company = $company;
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

    public function getCompany(): Company
    {
        return $this->company;
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
}
