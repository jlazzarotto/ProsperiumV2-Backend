<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity;

use App\Identity\Infrastructure\Persistence\Doctrine\DoctrineModuloRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineModuloRepository::class)]
#[ORM\Table(name: 'modulos')]
#[ORM\UniqueConstraint(name: 'uk_modulos_codigo', columns: ['codigo'])]
class Modulo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $codigo;

    #[ORM\Column(length: 255)]
    private string $nome;

    #[ORM\Column(name: 'categoria_codigo', length: 100, options: ['default' => 'sistema'])]
    private string $categoriaCodigo;

    #[ORM\Column(name: 'categoria_nome', length: 255, options: ['default' => 'Sistema'])]
    private string $categoriaNome;

    #[ORM\Column(name: 'menu_label', length: 255, nullable: true)]
    private ?string $menuLabel = null;

    #[ORM\Column(name: 'route_path', length: 255, nullable: true)]
    private ?string $routePath = null;

    #[ORM\Column(name: 'icon_key', length: 100, nullable: true)]
    private ?string $iconKey = null;

    #[ORM\Column(name: 'sort_order', type: 'integer', options: ['default' => 0])]
    private int $sortOrder = 0;

    #[ORM\Column(name: 'is_menu_entry', type: 'boolean', options: ['default' => true])]
    private bool $isMenuEntry = true;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getCategoriaCodigo(): string
    {
        return $this->categoriaCodigo;
    }

    public function getCategoriaNome(): string
    {
        return $this->categoriaNome;
    }

    public function getMenuLabel(): ?string
    {
        return $this->menuLabel;
    }

    public function getRoutePath(): ?string
    {
        return $this->routePath;
    }

    public function getIconKey(): ?string
    {
        return $this->iconKey;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function isMenuEntry(): bool
    {
        return $this->isMenuEntry;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
