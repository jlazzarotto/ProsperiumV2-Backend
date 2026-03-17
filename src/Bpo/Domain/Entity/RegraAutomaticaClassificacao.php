<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Entity;
use App\Bpo\Infrastructure\Persistence\Doctrine\DoctrineRegraAutomaticaClassificacaoRepository;
use App\Cadastro\Domain\Entity\CategoriaFinanceira;
use App\Cadastro\Domain\Entity\CentroCusto;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineRegraAutomaticaClassificacaoRepository::class)]
#[ORM\Table(name: 'regras_automaticas_classificacao')]
#[ORM\Index(name: 'idx_regras_auto_classificacao_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'status'])]
class RegraAutomaticaClassificacao
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: CategoriaFinanceira::class)] #[ORM\JoinColumn(name: 'categoria_financeira_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?CategoriaFinanceira $categoriaFinanceira;
    #[ORM\ManyToOne(targetEntity: CentroCusto::class)] #[ORM\JoinColumn(name: 'centro_custo_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')] private ?CentroCusto $centroCusto;
    #[ORM\Column(name: 'descricao_contains', length: 255)] private string $descricaoContains;
    #[ORM\Column(name: 'acao_notificacao', options: ['default' => true])] private bool $acaoNotificacao;
    #[ORM\Column(length: 30, options: ['default' => 'active'])] private string $status;
    public function __construct(int $companyId, ?Empresa $empresa, ?UnidadeNegocio $unidade, ?CategoriaFinanceira $categoriaFinanceira, ?CentroCusto $centroCusto, string $descricaoContains, bool $acaoNotificacao = true, string $status = 'active') { $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->categoriaFinanceira = $categoriaFinanceira; $this->centroCusto = $centroCusto; $this->descricaoContains = trim($descricaoContains); $this->acaoNotificacao = $acaoNotificacao; $this->status = $status; }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getEmpresa(): ?Empresa { return $this->empresa; } public function getUnidade(): ?UnidadeNegocio { return $this->unidade; } public function getCategoriaFinanceira(): ?CategoriaFinanceira { return $this->categoriaFinanceira; } public function getCentroCusto(): ?CentroCusto { return $this->centroCusto; } public function getDescricaoContains(): string { return $this->descricaoContains; } public function isAcaoNotificacao(): bool { return $this->acaoNotificacao; } public function getStatus(): string { return $this->status; }
    public function matches(string $texto): bool { return mb_stripos($texto, $this->descricaoContains) !== false; }
}
