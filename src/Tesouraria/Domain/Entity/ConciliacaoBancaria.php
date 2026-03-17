<?php

declare(strict_types=1);

namespace App\Tesouraria\Domain\Entity;

use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Financeiro\Domain\Entity\Baixa;
use App\Financeiro\Domain\Entity\MovimentoFinanceiro;
use App\Tesouraria\Infrastructure\Persistence\Doctrine\DoctrineConciliacaoBancariaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineConciliacaoBancariaRepository::class)]
#[ORM\Table(name: 'conciliacoes_bancarias')]
class ConciliacaoBancaria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])]
    private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)]
    #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: ExtratoBancario::class)]
    #[ORM\JoinColumn(name: 'extrato_bancario_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ExtratoBancario $extratoBancario;
    #[ORM\ManyToOne(targetEntity: MovimentoFinanceiro::class)]
    #[ORM\JoinColumn(name: 'movimento_financeiro_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?MovimentoFinanceiro $movimentoFinanceiro;
    #[ORM\ManyToOne(targetEntity: Baixa::class)]
    #[ORM\JoinColumn(name: 'baixa_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Baixa $baixa;
    #[ORM\Column(length: 20)]
    private string $modo;
    #[ORM\Column(length: 30, options: ['default' => 'confirmada'])]
    private string $status;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    public function __construct(int $companyId, Empresa $empresa, UnidadeNegocio $unidade, ExtratoBancario $extratoBancario, ?MovimentoFinanceiro $movimentoFinanceiro, ?Baixa $baixa, string $modo)
    { $this->company=$company; $this->empresa=$empresa; $this->unidade=$unidade; $this->extratoBancario=$extratoBancario; $this->movimentoFinanceiro=$movimentoFinanceiro; $this->baixa=$baixa; $this->modo=$modo; $this->status='confirmada'; $this->createdAt=new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; } public function getExtratoBancario(): ExtratoBancario { return $this->extratoBancario; } public function getModo(): string { return $this->modo; } public function getMovimentoFinanceiro(): ?MovimentoFinanceiro { return $this->movimentoFinanceiro; } public function getBaixa(): ?Baixa { return $this->baixa; }
}
