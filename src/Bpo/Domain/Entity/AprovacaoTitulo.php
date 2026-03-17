<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Entity;
use App\Bpo\Infrastructure\Persistence\Doctrine\DoctrineAprovacaoTituloRepository;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Financeiro\Domain\Entity\Titulo;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineAprovacaoTituloRepository::class)]
#[ORM\Table(name: 'aprovacoes_titulos')]
#[ORM\Index(name: 'idx_aprovacoes_titulos_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'status'])]
class AprovacaoTitulo
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: Titulo::class)] #[ORM\JoinColumn(name: 'titulo_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Titulo $titulo;
    #[ORM\Column(name: 'solicitante_user_id', type: 'bigint', options: ['unsigned' => true])] private int $solicitanteUserId;
    #[ORM\Column(name: 'tipo_operacao', length: 100)] private string $tipoOperacao;
    #[ORM\Column(name: 'valor_total', type: 'decimal', precision: 18, scale: 2)] private string $valorTotal;
    #[ORM\Column(length: 30, options: ['default' => 'pendente'])] private string $status;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] private \DateTimeImmutable $createdAt;
    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, Titulo $titulo, int $solicitanteUserId, string $tipoOperacao, string $valorTotal) { $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->titulo = $titulo; $this->solicitanteUserId = $solicitanteUserId; $this->tipoOperacao = trim($tipoOperacao); $this->valorTotal = $valorTotal; $this->status = 'pendente'; $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getEmpresa(): Empresa { return $this->empresa; } public function getUnidade(): UnidadeNegocio { return $this->unidade; } public function getTitulo(): Titulo { return $this->titulo; } public function getSolicitanteUserId(): int { return $this->solicitanteUserId; } public function getTipoOperacao(): string { return $this->tipoOperacao; } public function getValorTotal(): string { return $this->valorTotal; } public function getStatus(): string { return $this->status; }
}
