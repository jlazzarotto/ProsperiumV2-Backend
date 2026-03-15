<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Entity;

use App\Cadastro\Infrastructure\Persistence\Doctrine\DoctrineContaFinanceiraRepository;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineContaFinanceiraRepository::class)]
#[ORM\Table(name: 'contas_financeiras')]
#[ORM\UniqueConstraint(name: 'uk_contas_financeiras', columns: ['company_id', 'empresa_id', 'codigo'])]
#[ORM\Index(name: 'idx_contas_financeiras_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'status'])]
class ContaFinanceira
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Company $company;

    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Empresa $empresa;

    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)]
    #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?UnidadeNegocio $unidade;

    #[ORM\Column(length: 50)]
    private string $codigo;

    #[ORM\Column(length: 255)]
    private string $nome;

    #[ORM\Column(length: 50)]
    private string $tipo;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Company $company, Empresa $empresa, ?UnidadeNegocio $unidade, string $codigo, string $nome, string $tipo, string $status = 'active')
    {
        $now = new \DateTimeImmutable();
        $this->company = $company;
        $this->empresa = $empresa;
        $this->unidade = $unidade;
        $this->codigo = trim($codigo);
        $this->nome = trim($nome);
        $this->tipo = $tipo;
        $this->status = $status;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompany(): Company { return $this->company; }
    public function getEmpresa(): Empresa { return $this->empresa; }
    public function getUnidade(): ?UnidadeNegocio { return $this->unidade; }
    public function getCodigo(): string { return $this->codigo; }
    public function getNome(): string { return $this->nome; }
    public function getTipo(): string { return $this->tipo; }
    public function getStatus(): string { return $this->status; }
}
