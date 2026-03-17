<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Entity;

use App\Cadastro\Infrastructure\Persistence\Doctrine\DoctrinePessoaContatoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrinePessoaContatoRepository::class)]
#[ORM\Table(name: 'pessoa_contatos')]
#[ORM\Index(name: 'idx_pessoa_contatos_lookup', columns: ['company_id', 'pessoa_id'])]
class PessoaContato
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])]
    private int $companyId;

    #[ORM\ManyToOne(targetEntity: Pessoa::class)]
    #[ORM\JoinColumn(name: 'pessoa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Pessoa $pessoa;

    #[ORM\Column(name: 'nome_contato', length: 160)]
    private string $nomeContato;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $cargo;

    #[ORM\Column(length: 160, nullable: true)]
    private ?string $email;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $telefone;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $principal;

    #[ORM\Column(name: 'created_by', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $createdBy;

    #[ORM\Column(name: 'updated_by', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $updatedBy;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct(
        int $companyId,
        Pessoa $pessoa,
        string $nomeContato,
        bool $principal = false,
        ?string $cargo = null,
        ?string $email = null,
        ?string $telefone = null,
        ?int $createdBy = null,
    ) {
        $now = new \DateTimeImmutable();
        $this->companyId = $companyId;
        $this->pessoa = $pessoa;
        $this->nomeContato = trim($nomeContato);
        $this->cargo = $cargo !== null ? trim($cargo) : null;
        $this->email = $email !== null ? trim($email) : null;
        $this->telefone = $telefone !== null ? trim($telefone) : null;
        $this->principal = $principal;
        $this->createdBy = $createdBy;
        $this->updatedBy = $createdBy;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getPessoa(): Pessoa { return $this->pessoa; }
    public function getNomeContato(): string { return $this->nomeContato; }
    public function getCargo(): ?string { return $this->cargo; }
    public function getEmail(): ?string { return $this->email; }
    public function getTelefone(): ?string { return $this->telefone; }
    public function isPrincipal(): bool { return $this->principal; }
    public function getCreatedBy(): ?int { return $this->createdBy; }
    public function getUpdatedBy(): ?int { return $this->updatedBy; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function getDeletedAt(): ?\DateTimeImmutable { return $this->deletedAt; }

    public function update(
        string $nomeContato,
        bool $principal,
        ?string $cargo,
        ?string $email,
        ?string $telefone,
        ?int $updatedBy = null,
    ): void {
        $this->nomeContato = trim($nomeContato);
        $this->cargo = $cargo !== null ? trim($cargo) : null;
        $this->email = $email !== null ? trim($email) : null;
        $this->telefone = $telefone !== null ? trim($telefone) : null;
        $this->principal = $principal;
        $this->updatedBy = $updatedBy;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function softDelete(?int $updatedBy = null): void
    {
        $this->deletedAt = new \DateTimeImmutable();
        $this->updatedBy = $updatedBy;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
