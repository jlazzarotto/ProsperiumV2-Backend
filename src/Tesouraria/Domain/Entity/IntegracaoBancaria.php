<?php

declare(strict_types=1);

namespace App\Tesouraria\Domain\Entity;

use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Tesouraria\Infrastructure\Persistence\Doctrine\DoctrineIntegracaoBancariaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineIntegracaoBancariaRepository::class)]
#[ORM\Table(name: 'integracoes_bancarias')]
class IntegracaoBancaria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])]
    private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)]
    #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?UnidadeNegocio $unidade;
    #[ORM\ManyToOne(targetEntity: ContaFinanceira::class)]
    #[ORM\JoinColumn(name: 'conta_financeira_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ContaFinanceira $contaFinanceira;
    #[ORM\Column(length: 100)]
    private string $banco;
    #[ORM\Column(name: 'integration_type', length: 50)]
    private string $integrationType;
    #[ORM\Column(name: 'config_json', type: 'json')]
    private array $configJson;
    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;
    public function __construct(int $companyId, ?Empresa $empresa, ?UnidadeNegocio $unidade, ContaFinanceira $contaFinanceira, string $banco, string $integrationType, array $configJson, string $status='active')
    { $this->company=$company; $this->empresa=$empresa; $this->unidade=$unidade; $this->contaFinanceira=$contaFinanceira; $this->banco=trim($banco); $this->integrationType=trim($integrationType); $this->configJson=$configJson; $this->status=$status; }
}
