<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Entity;
use App\Contabil\Infrastructure\Persistence\Doctrine\DoctrineContaContabilRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineContaContabilRepository::class)]
#[ORM\Table(name: 'contas_contabeis')]
#[ORM\UniqueConstraint(name: 'uk_contas_contabeis_company_codigo', columns: ['company_id', 'codigo'])]
#[ORM\Index(name: 'idx_contas_contabeis_lookup', columns: ['company_id', 'tipo', 'status'])]
class ContaContabil
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: self::class)] #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?self $parent;
    #[ORM\Column(length: 50)] private string $codigo;
    #[ORM\Column(length: 255)] private string $nome;
    #[ORM\Column(length: 30)] private string $tipo;
    #[ORM\Column(length: 30, options: ['default' => 'active'])] private string $status;
    public function __construct(int $companyId, ?self $parent, string $codigo, string $nome, string $tipo, string $status = 'active') { $this->companyId = $companyId; $this->parent = $parent; $this->codigo = trim($codigo); $this->nome = trim($nome); $this->tipo = $tipo; $this->status = $status; }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getParent(): ?self { return $this->parent; } public function getCodigo(): string { return $this->codigo; } public function getNome(): string { return $this->nome; } public function getTipo(): string { return $this->tipo; } public function getStatus(): string { return $this->status; }
}
