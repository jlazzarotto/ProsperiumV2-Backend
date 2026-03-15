<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Entity;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Cobranca\Infrastructure\Persistence\Doctrine\DoctrinePixRecebimentoRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrinePixRecebimentoRepository::class)]
#[ORM\Table(name: 'pix_recebimentos')]
#[ORM\UniqueConstraint(name: 'uk_pix_recebimentos_e2e', columns: ['end_to_end_id'])]
class PixRecebimento
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Company::class)] #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Company $company;
    #[ORM\ManyToOne(targetEntity: Empresa::class)] #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)] #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: PixCobranca::class)] #[ORM\JoinColumn(name: 'pix_cobranca_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private PixCobranca $pixCobranca;
    #[ORM\Column(name: 'end_to_end_id', length: 120)] private string $endToEndId;
    #[ORM\Column(length: 100)] private string $txid;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)] private string $valor;
    #[ORM\Column(name: 'payload_json', type: 'json')] private array $payloadJson;
    #[ORM\Column(name: 'recebido_em', type: 'datetime_immutable')] private \DateTimeImmutable $recebidoEm;
    public function __construct(Company $company, Empresa $empresa, UnidadeNegocio $unidade, PixCobranca $pixCobranca, string $endToEndId, string $txid, string $valor, array $payloadJson, \DateTimeImmutable $recebidoEm) { $this->company = $company; $this->empresa = $empresa; $this->unidade = $unidade; $this->pixCobranca = $pixCobranca; $this->endToEndId = trim($endToEndId); $this->txid = trim($txid); $this->valor = $valor; $this->payloadJson = $payloadJson; $this->recebidoEm = $recebidoEm; }
}
