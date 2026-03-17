<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Entity;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Contabil\Infrastructure\Persistence\Doctrine\DoctrineIndicadorFinanceiroRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineIndicadorFinanceiroRepository::class)]
#[ORM\Table(name: 'indicadores_financeiros')]
#[ORM\Index(name: 'idx_indicadores_financeiros_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'codigo', 'data_referencia'])]
class IndicadorFinanceiro
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\Column(length: 100)] private string $codigo;
    #[ORM\Column(length: 255)] private string $nome;
    #[ORM\Column(name: 'data_referencia', type: 'date_immutable')] private \DateTimeImmutable $dataReferencia;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 4)] private string $valor;
    #[ORM\Column(name: 'metadata_json', type: 'json')] private array $metadataJson;
    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, string $codigo, string $nome, \DateTimeImmutable $dataReferencia, string $valor, array $metadataJson = []) { $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->codigo = trim($codigo); $this->nome = trim($nome); $this->dataReferencia = $dataReferencia; $this->valor = $valor; $this->metadataJson = $metadataJson; }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getCodigo(): string { return $this->codigo; } public function getNome(): string { return $this->nome; } public function getDataReferencia(): \DateTimeImmutable { return $this->dataReferencia; } public function getValor(): string { return $this->valor; } public function getMetadataJson(): array { return $this->metadataJson; }
}
