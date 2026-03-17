<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity\Tenant;

use App\Identity\Infrastructure\Persistence\Doctrine\DoctrinePerfilPermissaoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrinePerfilPermissaoRepository::class)]
#[ORM\Table(name: 'perfil_acesso_permissoes')]
#[ORM\UniqueConstraint(name: 'uk_pap', columns: ['perfil_acesso_id', 'permissao_id'])]
class PerfilPermissao
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Perfil::class)]
    #[ORM\JoinColumn(name: 'perfil_acesso_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Perfil $perfil;

    #[ORM\Column(name: 'permissao_id', type: 'bigint', options: ['unsigned' => true])]
    private int $permissaoId;

    public function __construct(Perfil $perfil, int $permissaoId)
    {
        $this->perfil = $perfil;
        $this->permissaoId = $permissaoId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerfil(): Perfil
    {
        return $this->perfil;
    }

    public function getPermissaoId(): int
    {
        return $this->permissaoId;
    }
}
