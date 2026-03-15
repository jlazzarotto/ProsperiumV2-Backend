<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Entity;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Cobranca\Infrastructure\Persistence\Doctrine\DoctrinePixEventoWebhookRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrinePixEventoWebhookRepository::class)]
#[ORM\Table(name: 'pix_eventos_webhook')]
#[ORM\Index(name: 'idx_pix_eventos_webhook_lookup', columns: ['company_id', 'tipo_evento', 'identificador_externo'])]
class PixEventoWebhook
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Company::class)] #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Company $company;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?UnidadeNegocio $unidade;
    #[ORM\Column(name: 'tipo_evento', length: 100)] private string $tipoEvento;
    #[ORM\Column(name: 'identificador_externo', length: 120, nullable: true)] private ?string $identificadorExterno;
    #[ORM\Column(name: 'payload_json', type: 'json')] private array $payloadJson;
    #[ORM\Column(name: 'recebido_em', type: 'datetime_immutable')] private \DateTimeImmutable $recebidoEm;
    public function __construct(Company $company, ?Empresa $empresa, ?UnidadeNegocio $unidade, string $tipoEvento, ?string $identificadorExterno, array $payloadJson, ?\DateTimeImmutable $recebidoEm = null) { $this->company = $company; $this->empresa = $empresa; $this->unidade = $unidade; $this->tipoEvento = trim($tipoEvento); $this->identificadorExterno = $identificadorExterno !== null ? trim($identificadorExterno) : null; $this->payloadJson = $payloadJson; $this->recebidoEm = $recebidoEm ?? new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
}
