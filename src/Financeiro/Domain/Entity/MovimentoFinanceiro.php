<?php

declare(strict_types=1);

namespace App\Financeiro\Domain\Entity;

use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Financeiro\Infrastructure\Persistence\Doctrine\DoctrineMovimentoFinanceiroRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineMovimentoFinanceiroRepository::class)]
#[ORM\Table(name: 'movimentos_financeiros')]
class MovimentoFinanceiro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])]
    private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)]
    #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: ContaFinanceira::class)]
    #[ORM\JoinColumn(name: 'conta_financeira_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ContaFinanceira $contaFinanceira;
    #[ORM\ManyToOne(targetEntity: Titulo::class)]
    #[ORM\JoinColumn(name: 'titulo_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Titulo $titulo;
    #[ORM\ManyToOne(targetEntity: Baixa::class)]
    #[ORM\JoinColumn(name: 'baixa_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Baixa $baixa;
    #[ORM\Column(length: 20)]
    private string $tipo;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $valor;
    #[ORM\Column(name: 'data_movimento', type: 'date_immutable')]
    private \DateTimeImmutable $dataMovimento;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $historico;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, ContaFinanceira $contaFinanceira, ?Titulo $titulo, ?Baixa $baixa, string $tipo, string $valor, \DateTimeImmutable $dataMovimento, ?string $historico)
    { $this->company=$company; $this->empresa=$empresa; $this->unidade=$unidade; $this->contaFinanceira=$contaFinanceira; $this->titulo=$titulo; $this->baixa=$baixa; $this->tipo=$tipo; $this->valor=$valor; $this->dataMovimento=$dataMovimento; $this->historico=$historico !== null ? trim($historico) : null; $this->createdAt=new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getEmpresa(): Empresa { return $this->empresa; } public function getUnidade(): UnidadeNegocio { return $this->unidade; } public function getContaFinanceira(): ContaFinanceira { return $this->contaFinanceira; } public function getTitulo(): ?Titulo { return $this->titulo; } public function getBaixa(): ?Baixa { return $this->baixa; } public function getTipo(): string { return $this->tipo; } public function getValor(): string { return $this->valor; } public function getDataMovimento(): \DateTimeImmutable { return $this->dataMovimento; } public function getHistorico(): ?string { return $this->historico; }
}
