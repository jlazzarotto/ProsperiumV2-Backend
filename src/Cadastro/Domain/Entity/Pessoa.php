<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Entity;

use App\Cadastro\Infrastructure\Persistence\Doctrine\DoctrinePessoaRepository;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrinePessoaRepository::class)]
#[ORM\Table(name: 'pessoas')]
#[ORM\Index(name: 'idx_pessoas_company', columns: ['company_id', 'status'])]
class Pessoa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Company $company;

    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Empresa $empresa;

    #[ORM\Column(length: 255)]
    private string $nome;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $documento;

    #[ORM\Column(length: 20)]
    private string $classificacao;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Company $company, ?Empresa $empresa, string $nome, ?string $documento, string $classificacao, string $status = 'active')
    {
        $now = new \DateTimeImmutable();
        $this->company = $company;
        $this->empresa = $empresa;
        $this->nome = trim($nome);
        $this->documento = $documento !== null ? trim($documento) : null;
        $this->classificacao = $classificacao;
        $this->status = $status;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompany(): Company { return $this->company; }
    public function getEmpresa(): ?Empresa { return $this->empresa; }
    public function getNome(): string { return $this->nome; }
    public function getDocumento(): ?string { return $this->documento; }
    public function getClassificacao(): string { return $this->classificacao; }
    public function getStatus(): string { return $this->status; }
}
