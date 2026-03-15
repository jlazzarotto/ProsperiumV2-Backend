<?php

declare(strict_types=1);

namespace App\Financeiro\Domain\Entity;

use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Financeiro\Infrastructure\Persistence\Doctrine\DoctrineBaixaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineBaixaRepository::class)]
#[ORM\Table(name: 'baixas')]
class Baixa
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
    #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: TituloParcela::class)]
    #[ORM\JoinColumn(name: 'titulo_parcela_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private TituloParcela $parcela;
    #[ORM\ManyToOne(targetEntity: ContaFinanceira::class)]
    #[ORM\JoinColumn(name: 'conta_financeira_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ContaFinanceira $contaFinanceira;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $valor;
    #[ORM\Column(name: 'data_pagamento', type: 'date_immutable')]
    private \DateTimeImmutable $dataPagamento;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $observacoes;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    public function __construct(Company $company, Empresa $empresa, UnidadeNegocio $unidade, TituloParcela $parcela, ContaFinanceira $contaFinanceira, string $valor, \DateTimeImmutable $dataPagamento, ?string $observacoes)
    { $this->company=$company; $this->empresa=$empresa; $this->unidade=$unidade; $this->parcela=$parcela; $this->contaFinanceira=$contaFinanceira; $this->valor=$valor; $this->dataPagamento=$dataPagamento; $this->observacoes=$observacoes !== null ? trim($observacoes) : null; $this->createdAt=new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getCompany(): Company { return $this->company; } public function getEmpresa(): Empresa { return $this->empresa; } public function getUnidade(): UnidadeNegocio { return $this->unidade; } public function getParcela(): TituloParcela { return $this->parcela; } public function getValor(): string { return $this->valor; } public function getDataPagamento(): \DateTimeImmutable { return $this->dataPagamento; } public function getContaFinanceira(): ContaFinanceira { return $this->contaFinanceira; }
}
