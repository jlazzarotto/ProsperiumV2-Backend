<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Entity;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Contabil\Infrastructure\Persistence\Doctrine\DoctrineSnapshotFluxoCaixaRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineSnapshotFluxoCaixaRepository::class)]
#[ORM\Table(name: 'snapshots_fluxo_caixa')]
#[ORM\UniqueConstraint(name: 'uk_snapshots_fluxo_caixa_contexto_data', columns: ['company_id', 'empresa_id', 'unidade_id', 'data_referencia'])]
class SnapshotFluxoCaixa
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\Column(name: 'data_referencia', type: 'date_immutable')] private \DateTimeImmutable $dataReferencia;
    #[ORM\Column(name: 'saldo_inicial', type: 'decimal', precision: 18, scale: 2)] private string $saldoInicial;
    #[ORM\Column(name: 'entradas_periodo', type: 'decimal', precision: 18, scale: 2)] private string $entradasPeriodo;
    #[ORM\Column(name: 'saidas_periodo', type: 'decimal', precision: 18, scale: 2)] private string $saidasPeriodo;
    #[ORM\Column(name: 'saldo_final', type: 'decimal', precision: 18, scale: 2)] private string $saldoFinal;
    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, \DateTimeImmutable $dataReferencia, string $saldoInicial, string $entradasPeriodo, string $saidasPeriodo, string $saldoFinal) { $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->dataReferencia = $dataReferencia; $this->saldoInicial = $saldoInicial; $this->entradasPeriodo = $entradasPeriodo; $this->saidasPeriodo = $saidasPeriodo; $this->saldoFinal = $saldoFinal; }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getDataReferencia(): \DateTimeImmutable { return $this->dataReferencia; } public function getSaldoInicial(): string { return $this->saldoInicial; } public function getEntradasPeriodo(): string { return $this->entradasPeriodo; } public function getSaidasPeriodo(): string { return $this->saidasPeriodo; } public function getSaldoFinal(): string { return $this->saldoFinal; }
    public function atualizarValores(string $saldoInicial, string $entradasPeriodo, string $saidasPeriodo, string $saldoFinal): void { $this->saldoInicial = $saldoInicial; $this->entradasPeriodo = $entradasPeriodo; $this->saidasPeriodo = $saidasPeriodo; $this->saldoFinal = $saldoFinal; }
}
