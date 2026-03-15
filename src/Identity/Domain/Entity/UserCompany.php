<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\Identity\Infrastructure\Persistence\Doctrine\DoctrineUserCompanyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineUserCompanyRepository::class)]
#[ORM\Table(name: 'user_companies')]
#[ORM\UniqueConstraint(name: 'uk_user_companies', columns: ['user_id', 'company_id'])]
class UserCompany
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

    #[ORM\Column(name: 'is_company_admin', type: 'boolean', options: ['default' => false])]
    private bool $isCompanyAdmin;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(User $user, Company $company, bool $isCompanyAdmin = false, string $status = 'active')
    {
        $this->user = $user;
        $this->company = $company;
        $this->isCompanyAdmin = $isCompanyAdmin;
        $this->status = $status;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function isCompanyAdmin(): bool
    {
        return $this->isCompanyAdmin;
    }
}
