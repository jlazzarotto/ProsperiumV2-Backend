<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Entity;

use App\Cadastro\Infrastructure\Persistence\Doctrine\DoctrineBancoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineBancoRepository::class)]
#[ORM\Table(name: 'bancos')]
#[ORM\UniqueConstraint(name: 'uk_bancos_codigo_compe', columns: ['codigo_compe'])]
class Banco
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'codigo_compe', length: 10)]
    private string $codigoCompe;

    #[ORM\Column(length: 180)]
    private string $nome;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $ispb;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $documento;

    #[ORM\Column(name: 'nome_curto', length: 120, nullable: true)]
    private ?string $nomeCurto;

    #[ORM\Column(name: 'rede', length: 30, nullable: true)]
    private ?string $rede;

    #[ORM\Column(name: 'tipo', length: 120, nullable: true)]
    private ?string $tipo;

    #[ORM\Column(name: 'tipo_pix', length: 20, nullable: true)]
    private ?string $tipoPix;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $site;

    #[ORM\Column(name: 'data_inicio_operacao', type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $dataInicioOperacao;

    #[ORM\Column(name: 'data_inicio_pix', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dataInicioPix;

    #[ORM\Column(name: 'data_registro_origem', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dataRegistroOrigem;

    #[ORM\Column(name: 'data_atualizacao_origem', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dataAtualizacaoOrigem;

    #[ORM\Column(length: 20, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $codigoCompe,
        string $nome,
        ?string $ispb = null,
        ?string $documento = null,
        ?string $nomeCurto = null,
        ?string $rede = null,
        ?string $tipo = null,
        ?string $tipoPix = null,
        ?string $site = null,
        ?\DateTimeImmutable $dataInicioOperacao = null,
        ?\DateTimeImmutable $dataInicioPix = null,
        ?\DateTimeImmutable $dataRegistroOrigem = null,
        ?\DateTimeImmutable $dataAtualizacaoOrigem = null,
        string $status = 'active',
    )
    {
        $now = new \DateTimeImmutable();
        $this->codigoCompe = trim($codigoCompe);
        $this->nome = trim($nome);
        $this->ispb = $ispb !== null ? trim($ispb) : null;
        $this->documento = $documento !== null ? trim($documento) : null;
        $this->nomeCurto = $nomeCurto !== null ? trim($nomeCurto) : null;
        $this->rede = $rede !== null ? trim($rede) : null;
        $this->tipo = $tipo !== null ? trim($tipo) : null;
        $this->tipoPix = $tipoPix !== null ? trim($tipoPix) : null;
        $this->site = $site !== null ? trim($site) : null;
        $this->dataInicioOperacao = $dataInicioOperacao;
        $this->dataInicioPix = $dataInicioPix;
        $this->dataRegistroOrigem = $dataRegistroOrigem;
        $this->dataAtualizacaoOrigem = $dataAtualizacaoOrigem;
        $this->status = $status;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCodigoCompe(): string { return $this->codigoCompe; }
    public function getNome(): string { return $this->nome; }
    public function getIspb(): ?string { return $this->ispb; }
    public function getDocumento(): ?string { return $this->documento; }
    public function getNomeCurto(): ?string { return $this->nomeCurto; }
    public function getRede(): ?string { return $this->rede; }
    public function getTipo(): ?string { return $this->tipo; }
    public function getTipoPix(): ?string { return $this->tipoPix; }
    public function getSite(): ?string { return $this->site; }
    public function getDataInicioOperacao(): ?\DateTimeImmutable { return $this->dataInicioOperacao; }
    public function getDataInicioPix(): ?\DateTimeImmutable { return $this->dataInicioPix; }
    public function getDataRegistroOrigem(): ?\DateTimeImmutable { return $this->dataRegistroOrigem; }
    public function getDataAtualizacaoOrigem(): ?\DateTimeImmutable { return $this->dataAtualizacaoOrigem; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}
