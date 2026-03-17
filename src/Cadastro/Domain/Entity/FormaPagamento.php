<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Entity;

use App\Cadastro\Infrastructure\Persistence\Doctrine\DoctrineFormaPagamentoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineFormaPagamentoRepository::class)]
#[ORM\Table(name: 'formas_pagamento')]
#[ORM\Index(name: 'idx_formas_pagamento_company', columns: ['company_id', 'status'])]
class FormaPagamento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])]
    private int $companyId;

    #[ORM\Column(length: 50)]
    private string $codigo;

    #[ORM\Column(length: 255)]
    private string $nome;

    #[ORM\Column(length: 50)]
    private string $tipo;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(int $companyId, string $codigo, string $nome, string $tipo, string $status = 'active')
    {
        $now = new \DateTimeImmutable();
        $this->companyId = $companyId;
        $this->codigo = trim($codigo);
        $this->nome = trim($nome);
        $this->tipo = $tipo;
        $this->status = $status;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getCodigo(): string { return $this->codigo; }
    public function getNome(): string { return $this->nome; }
    public function getTipo(): string { return $this->tipo; }
    public function getStatus(): string { return $this->status; }
}
