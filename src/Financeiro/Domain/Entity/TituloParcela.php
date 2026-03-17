<?php

declare(strict_types=1);

namespace App\Financeiro\Domain\Entity;

use App\Company\Domain\Entity\Tenant\Empresa;
use App\Company\Domain\Entity\Tenant\UnidadeNegocio;
use App\Financeiro\Infrastructure\Persistence\Doctrine\DoctrineTituloParcelaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineTituloParcelaRepository::class)]
#[ORM\Table(name: 'titulos_parcelas')]
#[ORM\Index(name: 'idx_titulos_parcelas_lookup', columns: ['company_id', 'empresa_id', 'unidade_id', 'vencimento', 'status'])]
class TituloParcela
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Titulo::class)]
    #[ORM\JoinColumn(name: 'titulo_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Titulo $titulo;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])]
    private int $companyId;
    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Empresa $empresa;
    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)]
    #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private UnidadeNegocio $unidade;
    #[ORM\Column]
    private int $numero;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $valor;
    #[ORM\Column(name: 'valor_aberto', type: 'decimal', precision: 18, scale: 2)]
    private string $valorAberto;
    #[ORM\Column(type: 'date_immutable')]
    private \DateTimeImmutable $vencimento;
    #[ORM\Column(length: 30, options: ['default' => 'aberto'])]
    private string $status;

    public function __construct(Titulo $titulo, Company $company, Empresa $empresa, UnidadeNegocio $unidade, int $numero, string $valor, \DateTimeImmutable $vencimento)
    {
        $this->titulo = $titulo; $this->companyId = $companyId; $this->empresa = $empresa; $this->unidade = $unidade; $this->numero = $numero; $this->valor = $valor; $this->valorAberto = $valor; $this->vencimento = $vencimento; $this->status = 'aberto';
    }
    public function getId(): ?int { return $this->id; }
    public function getTitulo(): Titulo { return $this->titulo; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getEmpresa(): Empresa { return $this->empresa; }
    public function getUnidade(): UnidadeNegocio { return $this->unidade; }
    public function getNumero(): int { return $this->numero; }
    public function getValor(): string { return $this->valor; }
    public function getValorAberto(): string { return $this->valorAberto; }
    public function getVencimento(): \DateTimeImmutable { return $this->vencimento; }
    public function getStatus(): string { return $this->status; }
    public function baixar(string $valor): void { $aberto = (float) $this->valorAberto - (float) $valor; $this->valorAberto = number_format(max($aberto, 0), 2, '.', ''); $this->status = $aberto <= 0.00001 ? 'liquidado' : 'parcial'; }
    public function resetarValorAberto(): void { $this->valorAberto = $this->valor; $this->status = 'aberto'; }
}
