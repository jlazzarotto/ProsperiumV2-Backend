<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Entity;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Contabil\Infrastructure\Persistence\Doctrine\DoctrineContaFinanceiraSaldoDiarioRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineContaFinanceiraSaldoDiarioRepository::class)]
#[ORM\Table(name: 'contas_financeiras_saldos_diarios')]
#[ORM\UniqueConstraint(name: 'uk_cfsd_contexto_data', columns: ['company_id', 'empresa_id', 'unidade_id', 'conta_financeira_id', 'data_saldo'])]
class ContaFinanceiraSaldoDiario
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Company::class)] #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Company $company;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: ContaFinanceira::class)] #[ORM\JoinColumn(name: 'conta_financeira_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private ContaFinanceira $contaFinanceira;
    #[ORM\Column(name: 'data_saldo', type: 'date_immutable')] private \DateTimeImmutable $dataSaldo;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)] private string $saldo;
    public function __construct(Company $company, Empresa $empresa, UnidadeNegocio $unidade, ContaFinanceira $contaFinanceira, \DateTimeImmutable $dataSaldo, string $saldo) { $this->company = $company; $this->empresa = $empresa; $this->unidade = $unidade; $this->contaFinanceira = $contaFinanceira; $this->dataSaldo = $dataSaldo; $this->saldo = $saldo; }
    public function getId(): ?int { return $this->id; } public function getDataSaldo(): \DateTimeImmutable { return $this->dataSaldo; } public function getSaldo(): string { return $this->saldo; } public function getContaFinanceira(): ContaFinanceira { return $this->contaFinanceira; }
    public function atualizarSaldo(string $saldo): void { $this->saldo = $saldo; }
}
