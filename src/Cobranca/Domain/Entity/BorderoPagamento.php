<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Entity;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Cobranca\Infrastructure\Persistence\Doctrine\DoctrineBorderoPagamentoRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineBorderoPagamentoRepository::class)]
#[ORM\Table(name: 'borderos_pagamento')]
class BorderoPagamento
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Company::class)] #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Company $company;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: ContaFinanceira::class)] #[ORM\JoinColumn(name: 'conta_financeira_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private ContaFinanceira $contaFinanceira;
    #[ORM\Column(length: 100)] private string $referencia;
    #[ORM\Column(length: 30, options: ['default' => 'rascunho'])] private string $status;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] private \DateTimeImmutable $createdAt;
    public function __construct(Company $company, Empresa $empresa, UnidadeNegocio $unidade, ContaFinanceira $contaFinanceira, string $referencia, string $status = 'rascunho') { $this->company = $company; $this->empresa = $empresa; $this->unidade = $unidade; $this->contaFinanceira = $contaFinanceira; $this->referencia = trim($referencia); $this->status = $status; $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getCompany(): Company { return $this->company; } public function getEmpresa(): Empresa { return $this->empresa; } public function getUnidade(): UnidadeNegocio { return $this->unidade; } public function getContaFinanceira(): ContaFinanceira { return $this->contaFinanceira; }
}
