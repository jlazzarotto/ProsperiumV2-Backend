<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Entity;

use App\Cadastro\Infrastructure\Persistence\Doctrine\DoctrinePaisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrinePaisRepository::class)]
#[ORM\Table(name: 'paises')]
#[ORM\UniqueConstraint(name: 'uk_paises_codigo_m49', columns: ['codigo_m49'])]
#[ORM\UniqueConstraint(name: 'uk_paises_iso_alpha2', columns: ['iso_alpha2'])]
#[ORM\UniqueConstraint(name: 'uk_paises_iso_alpha3', columns: ['iso_alpha3'])]
#[ORM\Index(name: 'idx_paises_status', columns: ['status'])]
class Pais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'codigo_m49', type: 'integer')]
    private int $codigoM49;

    #[ORM\Column(name: 'iso_alpha2', length: 2)]
    private string $isoAlpha2;

    #[ORM\Column(name: 'iso_alpha3', length: 3)]
    private string $isoAlpha3;

    #[ORM\Column(length: 180)]
    private string $nome;

    #[ORM\Column(name: 'regiao_codigo_m49', type: 'integer', nullable: true)]
    private ?int $regiaoCodigoM49;

    #[ORM\Column(name: 'regiao_nome', length: 120, nullable: true)]
    private ?string $regiaoNome;

    #[ORM\Column(name: 'sub_regiao_codigo_m49', type: 'integer', nullable: true)]
    private ?int $subRegiaoCodigoM49;

    #[ORM\Column(name: 'sub_regiao_nome', length: 160, nullable: true)]
    private ?string $subRegiaoNome;

    #[ORM\Column(name: 'regiao_intermediaria_codigo_m49', type: 'integer', nullable: true)]
    private ?int $regiaoIntermediariaCodigoM49;

    #[ORM\Column(name: 'regiao_intermediaria_nome', length: 160, nullable: true)]
    private ?string $regiaoIntermediariaNome;

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
        int $codigoM49,
        string $isoAlpha2,
        string $isoAlpha3,
        string $nome,
        ?int $regiaoCodigoM49,
        ?string $regiaoNome,
        ?int $subRegiaoCodigoM49,
        ?string $subRegiaoNome,
        ?int $regiaoIntermediariaCodigoM49,
        ?string $regiaoIntermediariaNome,
        string $hashPayload,
        string $origemDados = 'ibge',
        string $status = 'active',
    ) {
        $now = new \DateTimeImmutable();
        $this->codigoM49 = $codigoM49;
        $this->isoAlpha2 = trim($isoAlpha2);
        $this->isoAlpha3 = trim($isoAlpha3);
        $this->nome = trim($nome);
        $this->regiaoCodigoM49 = $regiaoCodigoM49;
        $this->regiaoNome = $regiaoNome !== null ? trim($regiaoNome) : null;
        $this->subRegiaoCodigoM49 = $subRegiaoCodigoM49;
        $this->subRegiaoNome = $subRegiaoNome !== null ? trim($subRegiaoNome) : null;
        $this->regiaoIntermediariaCodigoM49 = $regiaoIntermediariaCodigoM49;
        $this->regiaoIntermediariaNome = $regiaoIntermediariaNome !== null ? trim($regiaoIntermediariaNome) : null;
        $this->hashPayload = $hashPayload;
        $this->origemDados = $origemDados;
        $this->status = $status;
        $this->sincronizadoEm = $now;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCodigoM49(): int { return $this->codigoM49; }
    public function getIsoAlpha2(): string { return $this->isoAlpha2; }
    public function getIsoAlpha3(): string { return $this->isoAlpha3; }
    public function getNome(): string { return $this->nome; }
    public function getRegiaoCodigoM49(): ?int { return $this->regiaoCodigoM49; }
    public function getRegiaoNome(): ?string { return $this->regiaoNome; }
    public function getSubRegiaoCodigoM49(): ?int { return $this->subRegiaoCodigoM49; }
    public function getSubRegiaoNome(): ?string { return $this->subRegiaoNome; }
    public function getRegiaoIntermediariaCodigoM49(): ?int { return $this->regiaoIntermediariaCodigoM49; }
    public function getRegiaoIntermediariaNome(): ?string { return $this->regiaoIntermediariaNome; }
    public function getStatus(): string { return $this->status; }
    public function getHashPayload(): string { return $this->hashPayload; }

    public function updateFromSync(
        string $nome,
        ?int $regiaoCodigoM49,
        ?string $regiaoNome,
        ?int $subRegiaoCodigoM49,
        ?string $subRegiaoNome,
        ?int $regiaoIntermediariaCodigoM49,
        ?string $regiaoIntermediariaNome,
        string $hashPayload,
        string $status = 'active',
    ): void {
        $this->nome = trim($nome);
        $this->regiaoCodigoM49 = $regiaoCodigoM49;
        $this->regiaoNome = $regiaoNome !== null ? trim($regiaoNome) : null;
        $this->subRegiaoCodigoM49 = $subRegiaoCodigoM49;
        $this->subRegiaoNome = $subRegiaoNome !== null ? trim($subRegiaoNome) : null;
        $this->regiaoIntermediariaCodigoM49 = $regiaoIntermediariaCodigoM49;
        $this->regiaoIntermediariaNome = $regiaoIntermediariaNome !== null ? trim($regiaoIntermediariaNome) : null;
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
