<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Entity;

use App\Cadastro\Infrastructure\Persistence\Doctrine\DoctrineMunicipioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineMunicipioRepository::class)]
#[ORM\Table(name: 'municipios')]
#[ORM\UniqueConstraint(name: 'uk_municipios_codigo_ibge', columns: ['codigo_ibge'])]
#[ORM\Index(name: 'idx_municipios_uf_status', columns: ['uf_sigla', 'status'])]
class Municipio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'codigo_ibge', type: 'bigint', options: ['unsigned' => true])]
    private int $codigoIbge;

    #[ORM\Column(length: 180)]
    private string $nome;

    #[ORM\Column(name: 'uf_codigo_ibge', type: 'integer')]
    private int $ufCodigoIbge;

    #[ORM\Column(name: 'uf_sigla', length: 2)]
    private string $ufSigla;

    #[ORM\Column(name: 'uf_nome', length: 60)]
    private string $ufNome;

    #[ORM\Column(name: 'regiao_codigo_ibge', type: 'integer')]
    private int $regiaoCodigoIbge;

    #[ORM\Column(name: 'regiao_sigla', length: 2)]
    private string $regiaoSigla;

    #[ORM\Column(name: 'regiao_nome', length: 40)]
    private string $regiaoNome;

    #[ORM\Column(name: 'regiao_intermediaria_codigo_ibge', type: 'integer', nullable: true)]
    private ?int $regiaoIntermediariaCodigoIbge;

    #[ORM\Column(name: 'regiao_intermediaria_nome', length: 120, nullable: true)]
    private ?string $regiaoIntermediariaNome;

    #[ORM\Column(name: 'regiao_imediata_codigo_ibge', type: 'integer', nullable: true)]
    private ?int $regiaoImediataCodigoIbge;

    #[ORM\Column(name: 'regiao_imediata_nome', length: 120, nullable: true)]
    private ?string $regiaoImediataNome;

    #[ORM\Column(name: 'microrregiao_codigo_ibge', type: 'integer', nullable: true)]
    private ?int $microrregiaoCodigoIbge;

    #[ORM\Column(name: 'microrregiao_nome', length: 120, nullable: true)]
    private ?string $microrregiaoNome;

    #[ORM\Column(name: 'mesorregiao_codigo_ibge', type: 'integer', nullable: true)]
    private ?int $mesorregiaoCodigoIbge;

    #[ORM\Column(name: 'mesorregiao_nome', length: 120, nullable: true)]
    private ?string $mesorregiaoNome;

    #[ORM\Column(length: 20, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'hash_payload', length: 64)]
    private string $hashPayload;

    #[ORM\Column(name: 'origem_dados', length: 30)]
    private string $origemDados;

    #[ORM\Column(name: 'sincronizado_em', type: 'datetime_immutable')]
    private \DateTimeImmutable $sincronizadoEm;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        int $codigoIbge,
        string $nome,
        int $ufCodigoIbge,
        string $ufSigla,
        string $ufNome,
        int $regiaoCodigoIbge,
        string $regiaoSigla,
        string $regiaoNome,
        ?int $regiaoIntermediariaCodigoIbge,
        ?string $regiaoIntermediariaNome,
        ?int $regiaoImediataCodigoIbge,
        ?string $regiaoImediataNome,
        ?int $microrregiaoCodigoIbge,
        ?string $microrregiaoNome,
        ?int $mesorregiaoCodigoIbge,
        ?string $mesorregiaoNome,
        string $hashPayload,
        string $origemDados = 'ibge',
        string $status = 'active',
    ) {
        $now = new \DateTimeImmutable();
        $this->codigoIbge = $codigoIbge;
        $this->nome = trim($nome);
        $this->ufCodigoIbge = $ufCodigoIbge;
        $this->ufSigla = trim($ufSigla);
        $this->ufNome = trim($ufNome);
        $this->regiaoCodigoIbge = $regiaoCodigoIbge;
        $this->regiaoSigla = trim($regiaoSigla);
        $this->regiaoNome = trim($regiaoNome);
        $this->regiaoIntermediariaCodigoIbge = $regiaoIntermediariaCodigoIbge;
        $this->regiaoIntermediariaNome = $regiaoIntermediariaNome !== null ? trim($regiaoIntermediariaNome) : null;
        $this->regiaoImediataCodigoIbge = $regiaoImediataCodigoIbge;
        $this->regiaoImediataNome = $regiaoImediataNome !== null ? trim($regiaoImediataNome) : null;
        $this->microrregiaoCodigoIbge = $microrregiaoCodigoIbge;
        $this->microrregiaoNome = $microrregiaoNome !== null ? trim($microrregiaoNome) : null;
        $this->mesorregiaoCodigoIbge = $mesorregiaoCodigoIbge;
        $this->mesorregiaoNome = $mesorregiaoNome !== null ? trim($mesorregiaoNome) : null;
        $this->hashPayload = $hashPayload;
        $this->origemDados = $origemDados;
        $this->status = $status;
        $this->sincronizadoEm = $now;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCodigoIbge(): int { return $this->codigoIbge; }
    public function getNome(): string { return $this->nome; }
    public function getUfCodigoIbge(): int { return $this->ufCodigoIbge; }
    public function getUfSigla(): string { return $this->ufSigla; }
    public function getUfNome(): string { return $this->ufNome; }
    public function getRegiaoCodigoIbge(): int { return $this->regiaoCodigoIbge; }
    public function getRegiaoSigla(): string { return $this->regiaoSigla; }
    public function getRegiaoNome(): string { return $this->regiaoNome; }
    public function getRegiaoIntermediariaCodigoIbge(): ?int { return $this->regiaoIntermediariaCodigoIbge; }
    public function getRegiaoIntermediariaNome(): ?string { return $this->regiaoIntermediariaNome; }
    public function getRegiaoImediataCodigoIbge(): ?int { return $this->regiaoImediataCodigoIbge; }
    public function getRegiaoImediataNome(): ?string { return $this->regiaoImediataNome; }
    public function getMicrorregiaoCodigoIbge(): ?int { return $this->microrregiaoCodigoIbge; }
    public function getMicrorregiaoNome(): ?string { return $this->microrregiaoNome; }
    public function getMesorregiaoCodigoIbge(): ?int { return $this->mesorregiaoCodigoIbge; }
    public function getMesorregiaoNome(): ?string { return $this->mesorregiaoNome; }
    public function getStatus(): string { return $this->status; }
    public function getHashPayload(): string { return $this->hashPayload; }

    public function updateFromSync(
        string $nome,
        int $ufCodigoIbge,
        string $ufSigla,
        string $ufNome,
        int $regiaoCodigoIbge,
        string $regiaoSigla,
        string $regiaoNome,
        ?int $regiaoIntermediariaCodigoIbge,
        ?string $regiaoIntermediariaNome,
        ?int $regiaoImediataCodigoIbge,
        ?string $regiaoImediataNome,
        ?int $microrregiaoCodigoIbge,
        ?string $microrregiaoNome,
        ?int $mesorregiaoCodigoIbge,
        ?string $mesorregiaoNome,
        string $hashPayload,
        string $status = 'active',
    ): void {
        $this->nome = trim($nome);
        $this->ufCodigoIbge = $ufCodigoIbge;
        $this->ufSigla = trim($ufSigla);
        $this->ufNome = trim($ufNome);
        $this->regiaoCodigoIbge = $regiaoCodigoIbge;
        $this->regiaoSigla = trim($regiaoSigla);
        $this->regiaoNome = trim($regiaoNome);
        $this->regiaoIntermediariaCodigoIbge = $regiaoIntermediariaCodigoIbge;
        $this->regiaoIntermediariaNome = $regiaoIntermediariaNome !== null ? trim($regiaoIntermediariaNome) : null;
        $this->regiaoImediataCodigoIbge = $regiaoImediataCodigoIbge;
        $this->regiaoImediataNome = $regiaoImediataNome !== null ? trim($regiaoImediataNome) : null;
        $this->microrregiaoCodigoIbge = $microrregiaoCodigoIbge;
        $this->microrregiaoNome = $microrregiaoNome !== null ? trim($microrregiaoNome) : null;
        $this->mesorregiaoCodigoIbge = $mesorregiaoCodigoIbge;
        $this->mesorregiaoNome = $mesorregiaoNome !== null ? trim($mesorregiaoNome) : null;
        $this->hashPayload = $hashPayload;
        $this->status = $status;
        $this->sincronizadoEm = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function markInactive(): void
    {
        $this->status = 'inactive';
        $this->sincronizadoEm = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }
}
