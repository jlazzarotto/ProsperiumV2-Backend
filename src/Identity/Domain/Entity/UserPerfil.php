<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity;

use App\Company\Domain\Entity\Company;
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

    #[ORM\Column(name: 'perfil_acesso_id', type: 'bigint', options: ['unsigned' => true])]
    private int $perfilId;

    #[ORM\Column(name: 'empresa_id', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $empresaId;

    #[ORM\Column(name: 'unidade_id', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $unidadeId;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    public function __construct(User $user, Company $company, int $perfilId, ?int $empresaId = null, ?int $unidadeId = null, string $status = 'active')
    {
        $this->user = $user;
        $this->company = $company;
        $this->perfilId = $perfilId;
        $this->empresaId = $empresaId;
        $this->unidadeId = $unidadeId;
        $this->status = $status;
    }

    public function getPerfilId(): int
    {
        return $this->perfilId;
    }
}
