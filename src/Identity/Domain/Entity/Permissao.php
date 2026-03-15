<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity;

use App\Identity\Infrastructure\Persistence\Doctrine\DoctrinePermissaoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrinePermissaoRepository::class)]
#[ORM\Table(name: 'permissoes')]
#[ORM\UniqueConstraint(name: 'uk_permissoes_codigo', columns: ['codigo'])]
class Permissao
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Modulo::class)]
    #[ORM\JoinColumn(name: 'modulo_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Modulo $modulo;

    #[ORM\Column(length: 120)]
    private string $codigo;

    #[ORM\Column(length: 255)]
    private string $nome;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getModulo(): Modulo
    {
        return $this->modulo;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
