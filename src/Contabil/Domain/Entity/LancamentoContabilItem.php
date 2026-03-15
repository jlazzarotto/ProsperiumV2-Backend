<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Entity;
use App\Contabil\Infrastructure\Persistence\Doctrine\DoctrineLancamentoContabilItemRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineLancamentoContabilItemRepository::class)]
#[ORM\Table(name: 'lancamentos_contabeis_itens')]
class LancamentoContabilItem
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: LancamentoContabil::class)] #[ORM\JoinColumn(name: 'lancamento_contabil_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private LancamentoContabil $lancamento;
    #[ORM\ManyToOne(targetEntity: ContaContabil::class)] #[ORM\JoinColumn(name: 'conta_contabil_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private ContaContabil $contaContabil;
    #[ORM\Column(length: 10)] private string $natureza;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)] private string $valor;
    public function __construct(LancamentoContabil $lancamento, ContaContabil $contaContabil, string $natureza, string $valor) { $this->lancamento = $lancamento; $this->contaContabil = $contaContabil; $this->natureza = $natureza; $this->valor = $valor; }
    public function getId(): ?int { return $this->id; } public function getLancamento(): LancamentoContabil { return $this->lancamento; } public function getContaContabil(): ContaContabil { return $this->contaContabil; } public function getNatureza(): string { return $this->natureza; } public function getValor(): string { return $this->valor; }
}
