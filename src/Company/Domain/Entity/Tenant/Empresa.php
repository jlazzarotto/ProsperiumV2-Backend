<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity\Tenant;

use App\Company\Infrastructure\Persistence\Doctrine\DoctrineEmpresaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineEmpresaRepository::class)]
#[ORM\Table(name: 'empresas')]
#[ORM\UniqueConstraint(name: 'uk_empresas_company_cnpj', columns: ['company_id', 'cnpj'])]
class Empresa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])]
    private int $companyId;

    #[ORM\Column(name: 'razao_social', length: 255)]
    private string $razaoSocial;

    #[ORM\Column(name: 'nome_fantasia', length: 255, nullable: true)]
    private ?string $nomeFantasia;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apelido;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $abreviatura;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $cnpj;

    #[ORM\Column(name: 'inscricao_estadual', length: 50, nullable: true)]
    private ?string $inscricaoEstadual;

    #[ORM\Column(name: 'inscricao_municipal', length: 50, nullable: true)]
    private ?string $inscricaoMunicipal;

    #[ORM\Column(length: 9, nullable: true)]
    private ?string $cep;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $estado;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $cidade;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logradouro;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numero;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $complemento;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $bairro;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct(
        int $companyId,
        string $razaoSocial,
        ?string $nomeFantasia,
        ?string $cnpj,
        string $status = 'active',
        ?string $apelido = null,
        ?string $abreviatura = null,
        ?string $inscricaoEstadual = null,
        ?string $inscricaoMunicipal = null,
        ?string $cep = null,
        ?string $estado = null,
        ?string $cidade = null,
        ?string $logradouro = null,
        ?string $numero = null,
        ?string $complemento = null,
        ?string $bairro = null,
    ) {
        $now = new \DateTimeImmutable();
        $this->companyId = $companyId;
        $this->razaoSocial = trim($razaoSocial);
        $this->nomeFantasia = $nomeFantasia !== null ? trim($nomeFantasia) : null;
        $this->apelido = $apelido !== null ? trim($apelido) : null;
        $this->abreviatura = $abreviatura !== null ? strtoupper(trim($abreviatura)) : null;
        $this->cnpj = $cnpj;
        $this->inscricaoEstadual = $inscricaoEstadual !== null ? trim($inscricaoEstadual) : null;
        $this->inscricaoMunicipal = $inscricaoMunicipal !== null ? trim($inscricaoMunicipal) : null;
        $this->cep = $cep;
        $this->estado = $estado !== null ? trim($estado) : null;
        $this->cidade = $cidade !== null ? trim($cidade) : null;
        $this->logradouro = $logradouro !== null ? trim($logradouro) : null;
        $this->numero = $numero !== null ? trim($numero) : null;
        $this->complemento = $complemento !== null ? trim($complemento) : null;
        $this->bairro = $bairro !== null ? trim($bairro) : null;
        $this->status = $status;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getRazaoSocial(): string
    {
        return $this->razaoSocial;
    }

    public function getNomeFantasia(): ?string
    {
        return $this->nomeFantasia;
    }

    public function getApelido(): ?string
    {
        return $this->apelido;
    }

    public function getAbreviatura(): ?string
    {
        return $this->abreviatura;
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function getInscricaoEstadual(): ?string
    {
        return $this->inscricaoEstadual;
    }

    public function getInscricaoMunicipal(): ?string
    {
        return $this->inscricaoMunicipal;
    }

    public function getCep(): ?string
    {
        return $this->cep;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function getCidade(): ?string
    {
        return $this->cidade;
    }

    public function getLogradouro(): ?string
    {
        return $this->logradouro;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function getComplemento(): ?string
    {
        return $this->complemento;
    }

    public function getBairro(): ?string
    {
        return $this->bairro;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function update(
        int $companyId,
        string $razaoSocial,
        ?string $nomeFantasia,
        ?string $cnpj,
        string $status,
        ?string $apelido = null,
        ?string $abreviatura = null,
        ?string $inscricaoEstadual = null,
        ?string $inscricaoMunicipal = null,
        ?string $cep = null,
        ?string $estado = null,
        ?string $cidade = null,
        ?string $logradouro = null,
        ?string $numero = null,
        ?string $complemento = null,
        ?string $bairro = null,
    ): void {
        $this->companyId = $companyId;
        $this->razaoSocial = trim($razaoSocial);
        $this->nomeFantasia = $nomeFantasia !== null ? trim($nomeFantasia) : null;
        $this->apelido = $apelido !== null ? trim($apelido) : null;
        $this->abreviatura = $abreviatura !== null ? strtoupper(trim($abreviatura)) : null;
        $this->cnpj = $cnpj;
        $this->inscricaoEstadual = $inscricaoEstadual !== null ? trim($inscricaoEstadual) : null;
        $this->inscricaoMunicipal = $inscricaoMunicipal !== null ? trim($inscricaoMunicipal) : null;
        $this->cep = $cep;
        $this->estado = $estado !== null ? trim($estado) : null;
        $this->cidade = $cidade !== null ? trim($cidade) : null;
        $this->logradouro = $logradouro !== null ? trim($logradouro) : null;
        $this->numero = $numero !== null ? trim($numero) : null;
        $this->complemento = $complemento !== null ? trim($complemento) : null;
        $this->bairro = $bairro !== null ? trim($bairro) : null;
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function softDelete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
        $this->status = 'inactive';
        $this->updatedAt = new \DateTimeImmutable();
    }
}
