<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity\Tenant;

use App\Identity\Infrastructure\Persistence\Doctrine\DoctrinePerfilRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrinePerfilRepository::class)]
#[ORM\Table(name: 'perfis_acesso')]
#[ORM\UniqueConstraint(name: 'uk_perfis_acesso_company_codigo', columns: ['company_id', 'codigo'])]
class Perfil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'company_id', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $companyId;

    #[ORM\Column(length: 100)]
    private string $codigo;

    #[ORM\Column(length: 255)]
    private string $nome;

    #[ORM\Column(length: 20, options: ['default' => 'custom'])]
    private string $tipo;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    public function __construct(?int $companyId, string $codigo, string $nome, string $tipo = 'custom', string $status = 'active')
    {
        $this->companyId = $companyId;
        $this->codigo = trim($codigo);
        $this->nome = trim($nome);
        $this->tipo = $tipo;
        $this->status = $status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function update(string $nome, string $tipo, string $status): void
    {
        $this->nome = trim($nome);
        $this->tipo = $tipo;
        $this->status = $status;
    }
}
