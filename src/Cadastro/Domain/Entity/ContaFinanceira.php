<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Entity;

use App\Cadastro\Infrastructure\Persistence\Doctrine\DoctrineContaFinanceiraRepository;
use App\Cadastro\Domain\Entity\Banco;
use App\Cadastro\Domain\Entity\Pessoa;
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

    #[ORM\ManyToOne(targetEntity: Banco::class)]
    #[ORM\JoinColumn(name: 'banco_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Banco $banco;

    #[ORM\Column(length: 50)]
    private string $codigo;

    #[ORM\Column(length: 255)]
    private string $nome;

    #[ORM\Column(length: 50)]
    private string $tipo;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $agencia;

    #[ORM\Column(name: 'conta_numero', length: 30, nullable: true)]
    private ?string $contaNumero;

    #[ORM\Column(name: 'conta_digito', length: 5, nullable: true)]
    private ?string $contaDigito;

    #[ORM\ManyToOne(targetEntity: Pessoa::class)]
    #[ORM\JoinColumn(name: 'titular_pessoa_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Pessoa $titularPessoa;

    #[ORM\Column(name: 'saldo_inicial', type: 'decimal', precision: 18, scale: 2, options: ['default' => 0])]
    private string $saldoInicial;

    #[ORM\Column(name: 'data_saldo_inicial', type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $dataSaldoInicial;

    #[ORM\Column(name: 'permite_movimento_negativo', type: 'boolean', options: ['default' => false])]
    private bool $permiteMovimentoNegativo;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Company $company, Empresa $empresa, ?UnidadeNegocio $unidade, ?Banco $banco, ?Pessoa $titularPessoa, string $codigo, string $nome, string $tipo, ?string $agencia = null, ?string $contaNumero = null, ?string $contaDigito = null, float $saldoInicial = 0.0, ?\DateTimeImmutable $dataSaldoInicial = null, bool $permiteMovimentoNegativo = false, string $status = 'active')
    {
        $now = new \DateTimeImmutable();
        $this->company = $company;
        $this->empresa = $empresa;
        $this->unidade = $unidade;
        $this->banco = $banco;
        $this->titularPessoa = $titularPessoa;
        $this->codigo = trim($codigo);
        $this->nome = trim($nome);
        $this->tipo = trim($tipo);
        $this->agencia = $agencia !== null ? trim($agencia) : null;
        $this->contaNumero = $contaNumero !== null ? trim($contaNumero) : null;
        $this->contaDigito = $contaDigito !== null ? trim($contaDigito) : null;
        $this->saldoInicial = number_format($saldoInicial, 2, '.', '');
        $this->dataSaldoInicial = $dataSaldoInicial;
        $this->permiteMovimentoNegativo = $permiteMovimentoNegativo;
        $this->status = $status;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompany(): Company { return $this->company; }
    public function getEmpresa(): Empresa { return $this->empresa; }
    public function getUnidade(): ?UnidadeNegocio { return $this->unidade; }
    public function getBanco(): ?Banco { return $this->banco; }
    public function getTitularPessoa(): ?Pessoa { return $this->titularPessoa; }
    public function getCodigo(): string { return $this->codigo; }
    public function getNome(): string { return $this->nome; }
    public function getTipo(): string { return $this->tipo; }
    public function getAgencia(): ?string { return $this->agencia; }
    public function getContaNumero(): ?string { return $this->contaNumero; }
    public function getContaDigito(): ?string { return $this->contaDigito; }
    public function getSaldoInicial(): string { return $this->saldoInicial; }
    public function getDataSaldoInicial(): ?\DateTimeImmutable { return $this->dataSaldoInicial; }
    public function isPermiteMovimentoNegativo(): bool { return $this->permiteMovimentoNegativo; }
    public function getStatus(): string { return $this->status; }

    public function update(Empresa $empresa, ?UnidadeNegocio $unidade, ?Banco $banco, ?Pessoa $titularPessoa, string $codigo, string $nome, string $tipo, ?string $agencia, ?string $contaNumero, ?string $contaDigito, float $saldoInicial, ?\DateTimeImmutable $dataSaldoInicial, bool $permiteMovimentoNegativo, string $status): void
    {
        $this->empresa = $empresa;
        $this->unidade = $unidade;
        $this->banco = $banco;
        $this->titularPessoa = $titularPessoa;
        $this->codigo = trim($codigo);
        $this->nome = trim($nome);
        $this->tipo = trim($tipo);
        $this->agencia = $agencia !== null ? trim($agencia) : null;
        $this->contaNumero = $contaNumero !== null ? trim($contaNumero) : null;
        $this->contaDigito = $contaDigito !== null ? trim($contaDigito) : null;
        $this->saldoInicial = number_format($saldoInicial, 2, '.', '');
        $this->dataSaldoInicial = $dataSaldoInicial;
        $this->permiteMovimentoNegativo = $permiteMovimentoNegativo;
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
