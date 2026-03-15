<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity;

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

    #[ORM\ManyToOne(targetEntity: Permissao::class)]
    #[ORM\JoinColumn(name: 'permissao_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Permissao $permissao;

    public function __construct(Perfil $perfil, Permissao $permissao)
    {
        $this->perfil = $perfil;
        $this->permissao = $permissao;
    }
}
