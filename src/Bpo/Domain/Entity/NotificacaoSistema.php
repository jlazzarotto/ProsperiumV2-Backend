<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Entity;
use App\Bpo\Infrastructure\Persistence\Doctrine\DoctrineNotificacaoSistemaRepository;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Identity\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineNotificacaoSistemaRepository::class)]
#[ORM\Table(name: 'notificacoes_sistema')]
#[ORM\Index(name: 'idx_notificacoes_sistema_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'status'])]
class NotificacaoSistema
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Company::class)] #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Company $company;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: User::class)] #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?User $user;
    #[ORM\Column(length: 100)] private string $tipo;
    #[ORM\Column(length: 255)] private string $titulo;
    #[ORM\Column(type: 'text')] private string $mensagem;
    #[ORM\Column(name: 'metadata_json', type: 'json')] private array $metadataJson;
    #[ORM\Column(length: 30, options: ['default' => 'pending'])] private string $status;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] private \DateTimeImmutable $createdAt;
    public function __construct(Company $company, ?Empresa $empresa, ?UnidadeNegocio $unidade, ?User $user, string $tipo, string $titulo, string $mensagem, array $metadataJson = [], string $status = 'pending') { $this->company = $company; $this->empresa = $empresa; $this->unidade = $unidade; $this->user = $user; $this->tipo = trim($tipo); $this->titulo = trim($titulo); $this->mensagem = trim($mensagem); $this->metadataJson = $metadataJson; $this->status = $status; $this->createdAt = new \DateTimeImmutable(); }
}
