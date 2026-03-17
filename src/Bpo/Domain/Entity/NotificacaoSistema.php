<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Entity;
use App\Bpo\Infrastructure\Persistence\Doctrine\DoctrineNotificacaoSistemaRepository;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineNotificacaoSistemaRepository::class)]
#[ORM\Table(name: 'notificacoes_sistema')]
#[ORM\Index(name: 'idx_notificacoes_sistema_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'status'])]
class NotificacaoSistema
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?UnidadeNegocio $unidade;
    #[ORM\Column(name: 'user_id', type: 'bigint', nullable: true, options: ['unsigned' => true])] private ?int $userId;
    #[ORM\Column(length: 100)] private string $tipo;
    #[ORM\Column(length: 255)] private string $titulo;
    #[ORM\Column(type: 'text')] private string $mensagem;
    #[ORM\Column(name: 'metadata_json', type: 'json')] private array $metadataJson;
    #[ORM\Column(length: 30, options: ['default' => 'pending'])] private string $status;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] private \DateTimeImmutable $createdAt;
    public function __construct(int $companyId, ?Empresa $empresa, ?UnidadeNegocio $unidade, ?int $userId, string $tipo, string $titulo, string $mensagem, array $metadataJson = [], string $status = 'pending') { $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->userId = $userId; $this->tipo = trim($tipo); $this->titulo = trim($titulo); $this->mensagem = trim($mensagem); $this->metadataJson = $metadataJson; $this->status = $status; $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; }
}
