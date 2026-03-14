<?php

declare(strict_types=1);

namespace App\Module\Empresa\Domain\Entity;

use App\Module\Empresa\Infrastructure\Repository\EmpresaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpresaRepository::class)]
#[ORM\Table(name: 'empresas')]
class Empresa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_empresa')]
    private ?int $id = null;

    #[ORM\Column(name: 'razao_social', length: 255)]
    private string $razaoSocial;

    #[ORM\Column(name: 'nome_fantasia', length: 255, nullable: true)]
    private ?string $nomeFantasia = null;

    #[ORM\Column(name: 'cnpj', length: 20, nullable: true)]
    private ?string $cnpj = null;

    #[ORM\Column(name: 'status', length: 20)]
    private string $status = 'ativa';

    #[ORM\Column(name: 'created_at')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRazaoSocial(): string
    {
        return $this->razaoSocial;
    }

    public function getNomeFantasia(): ?string
    {
        return $this->nomeFantasia;
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
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

    public function setRazaoSocial(string $razaoSocial): void
    {
        $this->razaoSocial = trim($razaoSocial);
    }

    public function setNomeFantasia(?string $nomeFantasia): void
    {
        $this->nomeFantasia = $nomeFantasia !== null ? trim($nomeFantasia) : null;
    }

    public function setCnpj(?string $cnpj): void
    {
        $this->cnpj = $cnpj !== null ? trim($cnpj) : null;
    }

    public function inativar(): void
    {
        $this->status = 'inativa';
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function atualizar(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return array<string, int|string|null>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'razaoSocial' => $this->razaoSocial,
            'nomeFantasia' => $this->nomeFantasia,
            'cnpj' => $this->cnpj,
            'status' => $this->status,
        ];
    }
}
