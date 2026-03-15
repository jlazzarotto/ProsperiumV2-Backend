<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Identity\Infrastructure\Persistence\Doctrine\DoctrineUserUnidadeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineUserUnidadeRepository::class)]
#[ORM\Table(name: 'user_unidades')]
#[ORM\UniqueConstraint(name: 'uk_user_unidades', columns: ['user_id', 'unidade_id'])]
class UserUnidade
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

    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)]
    #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private UnidadeNegocio $unidade;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(User $user, Company $company, UnidadeNegocio $unidade, string $status = 'active')
    {
        $this->user = $user;
        $this->company = $company;
        $this->unidade = $unidade;
        $this->status = $status;
        $this->createdAt = new \DateTimeImmutable();
    }
}
