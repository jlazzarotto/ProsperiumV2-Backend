<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Entity;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Contabil\Infrastructure\Persistence\Doctrine\DoctrineLancamentoContabilRepository;
use App\Financeiro\Domain\Entity\Titulo;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineLancamentoContabilRepository::class)]
#[ORM\Table(name: 'lancamentos_contabeis')]
#[ORM\Index(name: 'idx_lancamentos_contabeis_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'data_lancamento', 'status'])]
class LancamentoContabil
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Company::class)] #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Company $company;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: Titulo::class)] #[ORM\JoinColumn(name: 'titulo_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?Titulo $titulo;
    #[ORM\Column(name: 'data_lancamento', type: 'date_immutable')] private \DateTimeImmutable $dataLancamento;
    #[ORM\Column(type: 'text')] private string $historico;
    #[ORM\Column(length: 30, options: ['default' => 'posted'])] private string $status;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] private \DateTimeImmutable $createdAt;
    public function __construct(Company $company, Empresa $empresa, UnidadeNegocio $unidade, ?Titulo $titulo, \DateTimeImmutable $dataLancamento, string $historico, string $status = 'posted') { $this->company = $company; $this->empresa = $empresa; $this->unidade = $unidade; $this->titulo = $titulo; $this->dataLancamento = $dataLancamento; $this->historico = trim($historico); $this->status = $status; $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getCompany(): Company { return $this->company; } public function getEmpresa(): Empresa { return $this->empresa; } public function getUnidade(): UnidadeNegocio { return $this->unidade; } public function getTitulo(): ?Titulo { return $this->titulo; } public function getDataLancamento(): \DateTimeImmutable { return $this->dataLancamento; } public function getHistorico(): string { return $this->historico; } public function getStatus(): string { return $this->status; }
}
