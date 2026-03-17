<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Entity;
use App\Bpo\Infrastructure\Persistence\Doctrine\DoctrineTarefaOperacionalBpoRepository;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Financeiro\Domain\Entity\Titulo;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineTarefaOperacionalBpoRepository::class)]
#[ORM\Table(name: 'tarefas_operacionais_bpo')]
#[ORM\Index(name: 'idx_tarefas_bpo_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'status', 'prioridade'])]
class TarefaOperacionalBpo
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: Titulo::class)] #[ORM\JoinColumn(name: 'titulo_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?Titulo $titulo;
    #[ORM\Column(name: 'responsavel_user_id', type: 'bigint', nullable: true, options: ['unsigned' => true])] private ?int $responsavelUserId;
    #[ORM\Column(length: 100)] private string $tipo;
    #[ORM\Column(type: 'text')] private string $descricao;
    #[ORM\Column(length: 20, options: ['default' => 'media'])] private string $prioridade;
    #[ORM\Column(length: 30, options: ['default' => 'aberta'])] private string $status;
    #[ORM\Column(name: 'prazo_em', type: 'datetime_immutable', nullable: true)] private ?\DateTimeImmutable $prazoEm;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] private \DateTimeImmutable $createdAt;
    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, ?Titulo $titulo, ?int $responsavelUserId, string $tipo, string $descricao, string $prioridade = 'media', ?\DateTimeImmutable $prazoEm = null) { $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->titulo = $titulo; $this->responsavelUserId = $responsavelUserId; $this->tipo = trim($tipo); $this->descricao = trim($descricao); $this->prioridade = $prioridade; $this->status = 'aberta'; $this->prazoEm = $prazoEm; $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getEmpresa(): Empresa { return $this->empresa; } public function getUnidade(): UnidadeNegocio { return $this->unidade; } public function getTitulo(): ?Titulo { return $this->titulo; } public function getResponsavelUserId(): ?int { return $this->responsavelUserId; } public function getTipo(): string { return $this->tipo; } public function getDescricao(): string { return $this->descricao; } public function getPrioridade(): string { return $this->prioridade; } public function getStatus(): string { return $this->status; } public function getPrazoEm(): ?\DateTimeImmutable { return $this->prazoEm; }
}
