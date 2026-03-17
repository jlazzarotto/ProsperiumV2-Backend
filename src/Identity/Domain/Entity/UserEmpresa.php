<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\Identity\Infrastructure\Persistence\Doctrine\DoctrineUserEmpresaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineUserEmpresaRepository::class)]
#[ORM\Table(name: 'user_empresas')]
#[ORM\UniqueConstraint(name: 'uk_user_empresas', columns: ['user_id', 'empresa_id'])]
class UserEmpresa
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

    #[ORM\Column(name: 'empresa_id', type: 'bigint', options: ['unsigned' => true])]
    private int $empresaId;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(User $user, Company $company, int $empresaId, string $status = 'active')
    {
        $this->user = $user;
        $this->company = $company;
        $this->empresaId = $empresaId;
        $this->status = $status;
        $this->createdAt = new \DateTimeImmutable();
    }
}
