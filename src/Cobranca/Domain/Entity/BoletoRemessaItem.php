<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Entity;
use App\Cobranca\Infrastructure\Persistence\Doctrine\DoctrineBoletoRemessaItemRepository;
use App\Financeiro\Domain\Entity\TituloParcela;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineBoletoRemessaItemRepository::class)]
#[ORM\Table(name: 'boletos_remessa_itens')]
#[ORM\UniqueConstraint(name: 'uk_boletos_remessa_itens_nosso_numero', columns: ['nosso_numero'])]
class BoletoRemessaItem
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: BoletoRemessa::class)] #[ORM\JoinColumn(name: 'boleto_remessa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private BoletoRemessa $remessa;
    #[ORM\ManyToOne(targetEntity: TituloParcela::class)] #[ORM\JoinColumn(name: 'titulo_parcela_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private TituloParcela $parcela;
    #[ORM\Column(name: 'nosso_numero', length: 100)] private string $nossoNumero;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)] private string $valor;
    #[ORM\Column(type: 'date_immutable')] private \DateTimeImmutable $vencimento;
    #[ORM\Column(length: 30, options: ['default' => 'pendente_registro'])] private string $status;
    #[ORM\Column(name: 'ocorrencia_retorno', length: 50, nullable: true)] private ?string $ocorrenciaRetorno = null;
    #[ORM\Column(name: 'data_ocorrencia', type: 'date_immutable', nullable: true)] private ?\DateTimeImmutable $dataOcorrencia = null;
    public function __construct(BoletoRemessa $remessa, TituloParcela $parcela, string $nossoNumero, string $valor, \DateTimeImmutable $vencimento, string $status = 'pendente_registro') { $this->remessa = $remessa; $this->parcela = $parcela; $this->nossoNumero = trim($nossoNumero); $this->valor = $valor; $this->vencimento = $vencimento; $this->status = $status; }
    public function getId(): ?int { return $this->id; } public function getRemessa(): BoletoRemessa { return $this->remessa; } public function getParcela(): TituloParcela { return $this->parcela; } public function getNossoNumero(): string { return $this->nossoNumero; } public function getValor(): string { return $this->valor; } public function getVencimento(): \DateTimeImmutable { return $this->vencimento; } public function getStatus(): string { return $this->status; } public function getOcorrenciaRetorno(): ?string { return $this->ocorrenciaRetorno; }
    public function registrarRetorno(string $ocorrencia, string $status, ?\DateTimeImmutable $dataOcorrencia = null): void { $this->ocorrenciaRetorno = trim($ocorrencia); $this->status = $status; $this->dataOcorrencia = $dataOcorrencia ?? new \DateTimeImmutable(); if ($status === 'liquidado') { $this->remessa->marcarStatus('processada'); } }
}
