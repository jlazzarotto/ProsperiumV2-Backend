<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Entity;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Cobranca\Infrastructure\Persistence\Doctrine\DoctrineBoletoRemessaRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineBoletoRemessaRepository::class)]
#[ORM\Table(name: 'boletos_remessa')]
#[ORM\UniqueConstraint(name: 'uk_boletos_remessa_codigo', columns: ['company_id', 'codigo_remessa'])]
#[ORM\Index(name: 'idx_boletos_remessa_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'status'])]
class BoletoRemessa
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: ContaFinanceira::class)] #[ORM\JoinColumn(name: 'conta_financeira_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private ContaFinanceira $contaFinanceira;
    #[ORM\Column(name: 'codigo_remessa', length: 100)] private string $codigoRemessa;
    #[ORM\Column(length: 100)] private string $banco;
    #[ORM\Column(length: 30, options: ['default' => 'gerada'])] private string $status;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] private \DateTimeImmutable $createdAt;
    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, ContaFinanceira $contaFinanceira, string $codigoRemessa, string $banco, string $status = 'gerada') { $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->contaFinanceira = $contaFinanceira; $this->codigoRemessa = trim($codigoRemessa); $this->banco = trim($banco); $this->status = $status; $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getEmpresa(): Empresa { return $this->empresa; } public function getUnidade(): UnidadeNegocio { return $this->unidade; } public function getContaFinanceira(): ContaFinanceira { return $this->contaFinanceira; } public function getCodigoRemessa(): string { return $this->codigoRemessa; } public function getBanco(): string { return $this->banco; } public function getStatus(): string { return $this->status; } public function marcarStatus(string $status): void { $this->status = $status; }
}
