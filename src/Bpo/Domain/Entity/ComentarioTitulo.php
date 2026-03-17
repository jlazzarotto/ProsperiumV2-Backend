<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Entity;
use App\Bpo\Infrastructure\Persistence\Doctrine\DoctrineComentarioTituloRepository;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Financeiro\Domain\Entity\Titulo;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineComentarioTituloRepository::class)]
#[ORM\Table(name: 'titulos_comentarios')]
class ComentarioTitulo
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: Titulo::class)] #[ORM\JoinColumn(name: 'titulo_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Titulo $titulo;
    #[ORM\Column(name: 'user_id', type: 'bigint', options: ['unsigned' => true])] private int $userId;
    #[ORM\Column(type: 'text')] private string $comentario;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] private \DateTimeImmutable $createdAt;
    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, Titulo $titulo, int $userId, string $comentario) { $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->titulo = $titulo; $this->userId = $userId; $this->comentario = trim($comentario); $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getTitulo(): Titulo { return $this->titulo; } public function getUserId(): int { return $this->userId; } public function getComentario(): string { return $this->comentario; } public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
