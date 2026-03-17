<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Entity;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Cobranca\Infrastructure\Persistence\Doctrine\DoctrineBoletoRetornoItemRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineBoletoRetornoItemRepository::class)]
#[ORM\Table(name: 'boletos_retorno_itens')]
#[ORM\Index(name: 'idx_boletos_retorno_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'nosso_numero'])]
class BoletoRetornoItem
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: BoletoRemessaItem::class)] #[ORM\JoinColumn(name: 'boleto_remessa_item_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?BoletoRemessaItem $remessaItem;
    #[ORM\Column(name: 'nosso_numero', length: 100)] private string $nossoNumero;
    #[ORM\Column(name: 'codigo_ocorrencia', length: 50)] private string $codigoOcorrencia;
    #[ORM\Column(length: 255, nullable: true)] private ?string $descricao;
    #[ORM\Column(name: 'valor_recebido', type: 'decimal', precision: 18, scale: 2)] private string $valorRecebido;
    #[ORM\Column(name: 'data_ocorrencia', type: 'date_immutable')] private \DateTimeImmutable $dataOcorrencia;
    #[ORM\Column(name: 'linha_original', type: 'text', nullable: true)] private ?string $linhaOriginal;
    #[ORM\Column(name: 'importado_em', type: 'datetime_immutable')] private \DateTimeImmutable $importadoEm;
    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, ?BoletoRemessaItem $remessaItem, string $nossoNumero, string $codigoOcorrencia, ?string $descricao, string $valorRecebido, \DateTimeImmutable $dataOcorrencia, ?string $linhaOriginal) { $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->remessaItem = $remessaItem; $this->nossoNumero = trim($nossoNumero); $this->codigoOcorrencia = trim($codigoOcorrencia); $this->descricao = $descricao !== null ? trim($descricao) : null; $this->valorRecebido = $valorRecebido; $this->dataOcorrencia = $dataOcorrencia; $this->linhaOriginal = $linhaOriginal; $this->importadoEm = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getNossoNumero(): string { return $this->nossoNumero; } public function getCodigoOcorrencia(): string { return $this->codigoOcorrencia; } public function getValorRecebido(): string { return $this->valorRecebido; } public function getDataOcorrencia(): \DateTimeImmutable { return $this->dataOcorrencia; }
}
