<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Entity;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Cobranca\Infrastructure\Persistence\Doctrine\DoctrineBorderoRecebimentoRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineBorderoRecebimentoRepository::class)]
#[ORM\Table(name: 'borderos_recebimento')]
class BorderoRecebimento
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: ContaFinanceira::class)] #[ORM\JoinColumn(name: 'conta_financeira_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private ContaFinanceira $contaFinanceira;
    #[ORM\Column(length: 100)] private string $referencia;
    #[ORM\Column(length: 30, options: ['default' => 'rascunho'])] private string $status;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] private \DateTimeImmutable $createdAt;
    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, ContaFinanceira $contaFinanceira, string $referencia, string $status = 'rascunho') { $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->contaFinanceira = $contaFinanceira; $this->referencia = trim($referencia); $this->status = $status; $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; }
}
