<?php

declare(strict_types=1);

namespace App\Tesouraria\Domain\Entity;

use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Financeiro\Domain\Entity\Baixa;
use App\Financeiro\Domain\Entity\MovimentoFinanceiro;
use App\Tesouraria\Infrastructure\Persistence\Doctrine\DoctrineExtratoBancarioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineExtratoBancarioRepository::class)]
#[ORM\Table(name: 'extratos_bancarios')]
#[ORM\Index(name: 'idx_extratos_bancarios_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'conta_financeira_id', 'data_movimento'])]
class ExtratoBancario
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
    #[ORM\Column(name: 'codigo_externo', length: 100, nullable: true)]
    private ?string $codigoExterno;
    #[ORM\Column(name: 'data_movimento', type: 'date_immutable')]
    private \DateTimeImmutable $dataMovimento;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $valor;
    #[ORM\Column(length: 20)]
    private string $tipo;
    #[ORM\Column(length: 255)]
    private string $descricao;
    #[ORM\Column(length: 30, options: ['default' => 'pendente'])]
    private string $status;
    #[ORM\ManyToOne(targetEntity: MovimentoFinanceiro::class)]
    #[ORM\JoinColumn(name: 'movimento_financeiro_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?MovimentoFinanceiro $movimentoFinanceiro = null;
    #[ORM\ManyToOne(targetEntity: Baixa::class)]
    #[ORM\JoinColumn(name: 'baixa_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Baixa $baixa = null;
    #[ORM\Column(name: 'importado_em', type: 'datetime_immutable')]
    private \DateTimeImmutable $importadoEm;

    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, ContaFinanceira $contaFinanceira, ?string $codigoExterno, \DateTimeImmutable $dataMovimento, string $valor, string $tipo, string $descricao)
    {
        $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->contaFinanceira = $contaFinanceira; $this->codigoExterno = $codigoExterno !== null ? trim($codigoExterno) : null; $this->dataMovimento = $dataMovimento; $this->valor = $valor; $this->tipo = $tipo; $this->descricao = trim($descricao); $this->status = 'pendente'; $this->importadoEm = new \DateTimeImmutable();
    }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getEmpresa(): Empresa { return $this->empresa; } public function getUnidade(): UnidadeNegocio { return $this->unidade; } public function getContaFinanceira(): ContaFinanceira { return $this->contaFinanceira; } public function getCodigoExterno(): ?string { return $this->codigoExterno; } public function getDataMovimento(): \DateTimeImmutable { return $this->dataMovimento; } public function getValor(): string { return $this->valor; } public function getTipo(): string { return $this->tipo; } public function getDescricao(): string { return $this->descricao; } public function getStatus(): string { return $this->status; } public function getMovimentoFinanceiro(): ?MovimentoFinanceiro { return $this->movimentoFinanceiro; } public function getBaixa(): ?Baixa { return $this->baixa; }
    public function conciliar(?MovimentoFinanceiro $movimento, ?Baixa $baixa): void { $this->movimentoFinanceiro = $movimento; $this->baixa = $baixa; $this->status = 'conciliado'; }
}
