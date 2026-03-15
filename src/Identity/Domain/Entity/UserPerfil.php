<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Identity\Infrastructure\Persistence\Doctrine\DoctrineUserPerfilRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineUserPerfilRepository::class)]
#[ORM\Table(name: 'user_perfis_acesso')]
#[ORM\UniqueConstraint(name: 'uk_user_perfis', columns: ['user_id', 'perfil_acesso_id', 'empresa_id', 'unidade_id'])]
class UserPerfil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Company $company;

    #[ORM\ManyToOne(targetEntity: Perfil::class)]
    #[ORM\JoinColumn(name: 'perfil_acesso_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Perfil $perfil;

    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Empresa $empresa;

    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)]
    #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?UnidadeNegocio $unidade;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    public function __construct(User $user, Company $company, Perfil $perfil, ?Empresa $empresa = null, ?UnidadeNegocio $unidade = null, string $status = 'active')
    {
        $this->user = $user;
        $this->company = $company;
        $this->perfil = $perfil;
        $this->empresa = $empresa;
        $this->unidade = $unidade;
        $this->status = $status;
    }
}
