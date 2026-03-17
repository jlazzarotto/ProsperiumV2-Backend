<?php

declare(strict_types=1);

namespace App\Financeiro\Domain\Entity;

use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Financeiro\Infrastructure\Persistence\Doctrine\DoctrineTituloRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineTituloRepository::class)]
#[ORM\Table(name: 'titulos')]
#[ORM\Index(name: 'idx_titulos_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'tipo', 'status'])]
class Titulo
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
    #[ORM\ManyToOne(targetEntity: Pessoa::class)]
    #[ORM\JoinColumn(name: 'pessoa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Pessoa $pessoa;
    #[ORM\Column(length: 20)]
    private string $tipo;
    #[ORM\Column(name: 'numero_documento', length: 100, nullable: true)]
    private ?string $numeroDocumento;
    #[ORM\Column(name: 'valor_total', type: 'decimal', precision: 18, scale: 2)]
    private string $valorTotal;
    #[ORM\Column(length: 30, options: ['default' => 'aberto'])]
    private string $status;
    #[ORM\Column(name: 'data_emissao', type: 'date_immutable')]
    private \DateTimeImmutable $dataEmissao;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $observacoes;
    #[ORM\ManyToOne(targetEntity: ContaFinanceira::class)]
    #[ORM\JoinColumn(name: 'conta_financeira_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?ContaFinanceira $contaFinanceira;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, Pessoa $pessoa, string $tipo, ?string $numeroDocumento, string $valorTotal, \DateTimeImmutable $dataEmissao, ?string $observacoes = null, ?ContaFinanceira $contaFinanceira = null)
    {
        $now = new \DateTimeImmutable();
        $this->companyId = $companyId;
        $this->empresa = $empresa;
        $this->unidade = $unidade;
        $this->pessoa = $pessoa;
        $this->tipo = $tipo;
        $this->numeroDocumento = $numeroDocumento !== null ? trim($numeroDocumento) : null;
        $this->valorTotal = $valorTotal;
        $this->status = 'aberto';
        $this->dataEmissao = $dataEmissao;
        $this->observacoes = $observacoes !== null ? trim($observacoes) : null;
        $this->contaFinanceira = $contaFinanceira;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getEmpresa(): Empresa { return $this->empresa; }
    public function getUnidade(): UnidadeNegocio { return $this->unidade; }
    public function getPessoa(): Pessoa { return $this->pessoa; }
    public function getTipo(): string { return $this->tipo; }
    public function getNumeroDocumento(): ?string { return $this->numeroDocumento; }
    public function getValorTotal(): string { return $this->valorTotal; }
    public function getStatus(): string { return $this->status; }
    public function getDataEmissao(): \DateTimeImmutable { return $this->dataEmissao; }
    public function getObservacoes(): ?string { return $this->observacoes; }
    public function getContaFinanceira(): ?ContaFinanceira { return $this->contaFinanceira; }
    public function marcarStatus(string $status): void { $this->status = $status; $this->updatedAt = new \DateTimeImmutable(); }
}
