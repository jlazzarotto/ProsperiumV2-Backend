<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\Identity\Infrastructure\Persistence\Doctrine\DoctrineUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: DoctrineUserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'uk_users_company_email', columns: ['company_id', 'email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ROOT = 'ROLE_ROOT';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const STATUS_ATIVO = 'ativo';
    public const STATUS_BLOQUEADO = 'bloqueado';
    public const STATUS_INATIVO = 'inativo';

    public const MAX_FAILED_ATTEMPTS = 5;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Company $company = null;

    #[ORM\Column(length: 255)]
    private string $nome;

    #[ORM\Column(length: 255)]
    private string $email;

    #[ORM\Column(name: 'password_hash', length: 255)]
    private string $passwordHash;

    #[ORM\Column(name: 'mfa_habilitado', type: 'boolean', options: ['default' => false])]
    private bool $mfaHabilitado;

    #[ORM\Column(name: 'mfa_secret', length: 255, nullable: true)]
    private ?string $mfaSecret = null;

    #[ORM\Column(name: 'ultimo_login', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $ultimoLogin = null;

    #[ORM\Column(name: 'failed_login_attempts', type: 'integer', options: ['unsigned' => true, 'default' => 0])]
    private int $failedLoginAttempts = 0;

    #[ORM\Column(name: 'locked_until', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lockedUntil = null;

    #[ORM\Column(length: 30, options: ['default' => self::ROLE_ADMIN])]
    private string $role;

    #[ORM\Column(length: 30, options: ['default' => self::STATUS_ATIVO])]
    private string $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $nome,
        string $email,
        string $passwordHash,
        string $status = self::STATUS_ATIVO,
        string $role = self::ROLE_ADMIN,
        ?Company $company = null
    ) {
        $now = new \DateTimeImmutable();
        $this->nome = trim($nome);
        $this->email = mb_strtolower(trim($email));
        $this->passwordHash = $passwordHash;
        $this->role = $role;
        $this->status = $status;
        $this->company = $company;
        $this->mfaHabilitado = false;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function isMfaHabilitado(): bool
    {
        return $this->mfaHabilitado;
    }

    public function getMfaSecret(): ?string
    {
        return $this->mfaSecret;
    }

    public function getUltimoLogin(): ?\DateTimeImmutable
    {
        return $this->ultimoLogin;
    }

    public function getFailedLoginAttempts(): int
    {
        return $this->failedLoginAttempts;
    }

    public function getLockedUntil(): ?\DateTimeImmutable
    {
        return $this->lockedUntil;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isRoot(): bool
    {
        return $this->role === self::ROLE_ROOT;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAtivo(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    public function isBloqueado(): bool
    {
        return $this->status === self::STATUS_BLOQUEADO;
    }

    public function isLocked(): bool
    {
        if ($this->lockedUntil === null) {
            return false;
        }

        return $this->lockedUntil > new \DateTimeImmutable();
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return array_values(array_unique([$this->role, 'ROLE_USER']));
    }

    public function eraseCredentials(): void
    {
    }

    public function getPassword(): string
    {
        return $this->passwordHash;
    }

    public function update(
        string $nome,
        string $email,
        string $status,
        string $role,
        ?Company $company = null,
        bool $mfaHabilitado = false
    ): void {
        $this->nome = trim($nome);
        $this->email = mb_strtolower(trim($email));
        $this->status = $status;
        $this->role = $role;
        $this->company = $company;
        $this->mfaHabilitado = $mfaHabilitado;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function changePassword(string $hashedPassword): void
    {
        $this->passwordHash = $hashedPassword;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function registerSuccessfulLogin(): void
    {
        $this->ultimoLogin = new \DateTimeImmutable();
        $this->failedLoginAttempts = 0;
        $this->lockedUntil = null;
    }

    public function registerFailedLogin(): void
    {
        $this->failedLoginAttempts++;

        if ($this->failedLoginAttempts >= self::MAX_FAILED_ATTEMPTS) {
            $this->status = self::STATUS_BLOQUEADO;
            $this->lockedUntil = new \DateTimeImmutable('+15 minutes');
        }
    }

    public function desbloquear(): void
    {
        $this->status = self::STATUS_ATIVO;
        $this->failedLoginAttempts = 0;
        $this->lockedUntil = null;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setMfaSecret(?string $secret): void
    {
        $this->mfaSecret = $secret;
        $this->mfaHabilitado = $secret !== null;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
