<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Entity;

use App\Cadastro\Infrastructure\Persistence\Doctrine\DoctrineUfRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineUfRepository::class)]
#[ORM\Table(name: 'ufs')]
#[ORM\UniqueConstraint(name: 'uk_ufs_codigo_ibge', columns: ['codigo_ibge'])]
#[ORM\UniqueConstraint(name: 'uk_ufs_sigla', columns: ['sigla'])]
#[ORM\Index(name: 'idx_ufs_status', columns: ['status'])]
class Uf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'codigo_ibge', type: 'integer')]
    private int $codigoIbge;

    #[ORM\Column(length: 2)]
    private string $sigla;

    #[ORM\Column(length: 60)]
    private string $nome;

    #[ORM\Column(name: 'regiao_codigo_ibge', type: 'integer')]
    private int $regiaoCodigoIbge;

    #[ORM\Column(name: 'regiao_sigla', length: 2)]
    private string $regiaoSigla;

    #[ORM\Column(name: 'regiao_nome', length: 40)]
    private string $regiaoNome;

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
        string $sigla,
        string $nome,
        int $regiaoCodigoIbge,
        string $regiaoSigla,
        string $regiaoNome,
        string $hashPayload,
        string $origemDados = 'ibge',
        string $status = 'active',
    ) {
        $now = new \DateTimeImmutable();
        $this->codigoIbge = $codigoIbge;
        $this->sigla = trim($sigla);
        $this->nome = trim($nome);
        $this->regiaoCodigoIbge = $regiaoCodigoIbge;
        $this->regiaoSigla = trim($regiaoSigla);
        $this->regiaoNome = trim($regiaoNome);
        $this->hashPayload = $hashPayload;
        $this->origemDados = $origemDados;
        $this->status = $status;
        $this->sincronizadoEm = $now;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCodigoIbge(): int { return $this->codigoIbge; }
    public function getSigla(): string { return $this->sigla; }
    public function getNome(): string { return $this->nome; }
    public function getRegiaoCodigoIbge(): int { return $this->regiaoCodigoIbge; }
    public function getRegiaoSigla(): string { return $this->regiaoSigla; }
    public function getRegiaoNome(): string { return $this->regiaoNome; }
    public function getStatus(): string { return $this->status; }
    public function getHashPayload(): string { return $this->hashPayload; }

    public function updateFromSync(
        string $nome,
        int $regiaoCodigoIbge,
        string $regiaoSigla,
        string $regiaoNome,
        string $hashPayload,
        string $status = 'active',
    ): void {
        $this->nome = trim($nome);
        $this->regiaoCodigoIbge = $regiaoCodigoIbge;
        $this->regiaoSigla = trim($regiaoSigla);
        $this->regiaoNome = trim($regiaoNome);
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
