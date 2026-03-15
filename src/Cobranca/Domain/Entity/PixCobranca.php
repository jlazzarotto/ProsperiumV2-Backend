<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Entity;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Cobranca\Infrastructure\Persistence\Doctrine\DoctrinePixCobrancaRepository;
use App\Financeiro\Domain\Entity\TituloParcela;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrinePixCobrancaRepository::class)]
#[ORM\Table(name: 'pix_cobrancas')]
#[ORM\UniqueConstraint(name: 'uk_pix_cobrancas_txid', columns: ['txid'])]
#[ORM\Index(name: 'idx_pix_cobrancas_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'status'])]
class PixCobranca
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Company::class)] #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Company $company;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: TituloParcela::class)] #[ORM\JoinColumn(name: 'titulo_parcela_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private TituloParcela $parcela;
    #[ORM\ManyToOne(targetEntity: ContaFinanceira::class)] #[ORM\JoinColumn(name: 'conta_financeira_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private ContaFinanceira $contaFinanceira;
    #[ORM\Column(length: 100)] private string $txid;
    #[ORM\Column(name: 'chave_pix', length: 255)] private string $chavePix;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)] private string $valor;
    #[ORM\Column(length: 30, options: ['default' => 'pendente'])] private string $status;
    #[ORM\Column(name: 'expiracao_segundos')] private int $expiracaoSegundos;
    #[ORM\Column(name: 'calendario_gerado_em', type: 'datetime_immutable')] private \DateTimeImmutable $calendarioGeradoEm;
    #[ORM\Column(name: 'calendario_expira_em', type: 'datetime_immutable')] private \DateTimeImmutable $calendarioExpiraEm;
    #[ORM\Column(name: 'qr_code', type: 'text', nullable: true)] private ?string $qrCode;
    #[ORM\Column(name: 'copia_cola', type: 'text', nullable: true)] private ?string $copiaCola;
    public function __construct(Company $company, Empresa $empresa, UnidadeNegocio $unidade, TituloParcela $parcela, ContaFinanceira $contaFinanceira, string $txid, string $chavePix, string $valor, int $expiracaoSegundos, ?string $qrCode = null, ?string $copiaCola = null) { $agora = new \DateTimeImmutable(); $this->company = $company; $this->empresa = $empresa; $this->unidade = $unidade; $this->parcela = $parcela; $this->contaFinanceira = $contaFinanceira; $this->txid = trim($txid); $this->chavePix = trim($chavePix); $this->valor = $valor; $this->status = 'pendente'; $this->expiracaoSegundos = $expiracaoSegundos; $this->calendarioGeradoEm = $agora; $this->calendarioExpiraEm = $agora->modify(sprintf('+%d seconds', $expiracaoSegundos)); $this->qrCode = $qrCode; $this->copiaCola = $copiaCola; }
    public function getId(): ?int { return $this->id; } public function getCompany(): Company { return $this->company; } public function getEmpresa(): Empresa { return $this->empresa; } public function getUnidade(): UnidadeNegocio { return $this->unidade; } public function getParcela(): TituloParcela { return $this->parcela; } public function getContaFinanceira(): ContaFinanceira { return $this->contaFinanceira; } public function getTxid(): string { return $this->txid; } public function getChavePix(): string { return $this->chavePix; } public function getValor(): string { return $this->valor; } public function getStatus(): string { return $this->status; } public function getExpiracaoSegundos(): int { return $this->expiracaoSegundos; } public function getCalendarioExpiraEm(): \DateTimeImmutable { return $this->calendarioExpiraEm; } public function getQrCode(): ?string { return $this->qrCode; } public function getCopiaCola(): ?string { return $this->copiaCola; }
    public function marcarRecebida(): void { $this->status = 'recebida'; }
}
