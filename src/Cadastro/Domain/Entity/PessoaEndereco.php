<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Entity;

use App\Cadastro\Infrastructure\Persistence\Doctrine\DoctrinePessoaEnderecoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrinePessoaEnderecoRepository::class)]
#[ORM\Table(name: 'pessoa_enderecos')]
#[ORM\Index(name: 'idx_pessoa_enderecos_lookup', columns: ['company_id', 'pessoa_id'])]
class PessoaEndereco
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

    #[ORM\Column(name: 'tipo_endereco', length: 20)]
    private string $tipoEndereco;

    #[ORM\Column(length: 180)]
    private string $logradouro;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numero;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $complemento;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $bairro;

    #[ORM\Column(length: 120)]
    private string $cidade;

    #[ORM\Column(type: 'string', length: 2, nullable: true)]
    private ?string $uf;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $cep;

    #[ORM\Column(length: 60, options: ['default' => 'Brasil'])]
    private string $pais;

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
        string $tipoEndereco,
        string $logradouro,
        string $cidade,
        string $pais = 'Brasil',
        bool $principal = false,
        ?string $numero = null,
        ?string $complemento = null,
        ?string $bairro = null,
        ?string $uf = null,
        ?string $cep = null,
        ?int $createdBy = null,
    ) {
        $now = new \DateTimeImmutable();
        $this->companyId = $companyId;
        $this->pessoa = $pessoa;
        $this->tipoEndereco = trim($tipoEndereco);
        $this->logradouro = trim($logradouro);
        $this->numero = $numero !== null ? trim($numero) : null;
        $this->complemento = $complemento !== null ? trim($complemento) : null;
        $this->bairro = $bairro !== null ? trim($bairro) : null;
        $this->cidade = trim($cidade);
        $this->uf = $uf !== null ? strtoupper(trim($uf)) : null;
        $this->cep = $cep !== null ? preg_replace('/\D+/', '', $cep) ?: null : null;
        $this->pais = trim($pais);
        $this->principal = $principal;
        $this->createdBy = $createdBy;
        $this->updatedBy = $createdBy;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getPessoa(): Pessoa { return $this->pessoa; }
    public function getTipoEndereco(): string { return $this->tipoEndereco; }
    public function getLogradouro(): string { return $this->logradouro; }
    public function getNumero(): ?string { return $this->numero; }
    public function getComplemento(): ?string { return $this->complemento; }
    public function getBairro(): ?string { return $this->bairro; }
    public function getCidade(): string { return $this->cidade; }
    public function getUf(): ?string { return $this->uf; }
    public function getCep(): ?string { return $this->cep; }
    public function getPais(): string { return $this->pais; }
    public function isPrincipal(): bool { return $this->principal; }
    public function getCreatedBy(): ?int { return $this->createdBy; }
    public function getUpdatedBy(): ?int { return $this->updatedBy; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function getDeletedAt(): ?\DateTimeImmutable { return $this->deletedAt; }

    public function update(
        string $tipoEndereco,
        string $logradouro,
        string $cidade,
        string $pais,
        bool $principal,
        ?string $numero,
        ?string $complemento,
        ?string $bairro,
        ?string $uf,
        ?string $cep,
        ?int $updatedBy = null,
    ): void {
        $this->tipoEndereco = trim($tipoEndereco);
        $this->logradouro = trim($logradouro);
        $this->numero = $numero !== null ? trim($numero) : null;
        $this->complemento = $complemento !== null ? trim($complemento) : null;
        $this->bairro = $bairro !== null ? trim($bairro) : null;
        $this->cidade = trim($cidade);
        $this->uf = $uf !== null ? strtoupper(trim($uf)) : null;
        $this->cep = $cep !== null ? preg_replace('/\D+/', '', $cep) ?: null : null;
        $this->pais = trim($pais);
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
