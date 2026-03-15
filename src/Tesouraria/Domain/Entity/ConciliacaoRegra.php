<?php

declare(strict_types=1);

namespace App\Tesouraria\Domain\Entity;

use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Tesouraria\Infrastructure\Persistence\Doctrine\DoctrineConciliacaoRegraRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineConciliacaoRegraRepository::class)]
#[ORM\Table(name: 'conciliacao_regras')]
class ConciliacaoRegra
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
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)]
    #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: ContaFinanceira::class)]
    #[ORM\JoinColumn(name: 'conta_financeira_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?ContaFinanceira $contaFinanceira;
    #[ORM\Column(name: 'descricao_contains', length: 255)]
    private string $descricaoContains;
    #[ORM\Column(name: 'tipo_movimento_sugerido', length: 20)]
    private string $tipoMovimentoSugerido;
    #[ORM\Column(name: 'aplicacao', length: 20)]
    private string $aplicacao;
    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;
    public function __construct(Company $company, ?Empresa $empresa, ?UnidadeNegocio $unidade, ?ContaFinanceira $contaFinanceira, string $descricaoContains, string $tipoMovimentoSugerido, string $aplicacao, string $status='active')
    { $this->company=$company; $this->empresa=$empresa; $this->unidade=$unidade; $this->contaFinanceira=$contaFinanceira; $this->descricaoContains=mb_strtolower(trim($descricaoContains)); $this->tipoMovimentoSugerido=$tipoMovimentoSugerido; $this->aplicacao=$aplicacao; $this->status=$status; }
    public function getId(): ?int { return $this->id; } public function getCompany(): Company { return $this->company; } public function getEmpresa(): ?Empresa { return $this->empresa; } public function getUnidade(): ?UnidadeNegocio { return $this->unidade; } public function getContaFinanceira(): ?ContaFinanceira { return $this->contaFinanceira; } public function getDescricaoContains(): string { return $this->descricaoContains; } public function getTipoMovimentoSugerido(): string { return $this->tipoMovimentoSugerido; } public function getAplicacao(): string { return $this->aplicacao; } public function getStatus(): string { return $this->status; }
}
