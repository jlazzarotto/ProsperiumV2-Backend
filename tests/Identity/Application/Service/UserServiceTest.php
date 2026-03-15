<?php

declare(strict_types=1);

namespace App\Tests\Identity\Application\Service;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Identity\Application\DTO\CreateUserRequest;
use App\Identity\Application\Service\UserService;
use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Identity\Domain\Repository\UserEmpresaRepositoryInterface;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Identity\Domain\Repository\UserUnidadeRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validation;

final class UserServiceTest extends TestCase
{
    public function testCreateAdminUserForCompany(): void
    {
        $userRepository = new InMemoryUserRepository();
        $userRepository->save(new User('Root', 'root@example.com', 'hash', 'active', User::ROLE_ROOT));
        $service = new UserService(
            $userRepository,
            new UserCompanyRepositorySpy(),
            new UserEmpresaRepositorySpy(),
            new UserUnidadeRepositorySpy(),
            new FixedCompanyRepository(),
            new EmptyEmpresaRepository(),
            new EmptyUnidadeRepository(),
            new RequestValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
            new ImmediateTransactionRunnerIdentity(),
            new FixedPasswordHasher(),
            new AuditoriaLogger($this->createMock(Connection::class))
        );

        $request = new CreateUserRequest();
        $request->nome = 'Admin';
        $request->email = 'admin@example.com';
        $request->password = 'Password123';
        $request->companyId = 1;
        $request->role = User::ROLE_ADMIN;

        $user = $service->create($request);

        self::assertSame('admin@example.com', $user->getEmail());
        self::assertSame(User::ROLE_ADMIN, $user->getRole());
        self::assertSame(2, $userRepository->countAll());
    }

    public function testCreateFirstUserRequiresRootRole(): void
    {
        $service = new UserService(
            new InMemoryUserRepository(),
            new UserCompanyRepositorySpy(),
            new UserEmpresaRepositorySpy(),
            new UserUnidadeRepositorySpy(),
            new FixedCompanyRepository(),
            new EmptyEmpresaRepository(),
            new EmptyUnidadeRepository(),
            new RequestValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
            new ImmediateTransactionRunnerIdentity(),
            new FixedPasswordHasher(),
            new AuditoriaLogger($this->createMock(Connection::class))
        );

        $request = new CreateUserRequest();
        $request->nome = 'Admin';
        $request->email = 'admin@example.com';
        $request->password = 'Password123';
        $request->companyId = 1;
        $request->role = User::ROLE_ADMIN;

        $this->expectException(\App\Shared\Domain\Exception\ValidationException::class);
        $service->create($request);
    }
}

final class ImmediateTransactionRunnerIdentity implements TransactionRunnerInterface
{
    public function run(callable $operation): mixed { return $operation(); }
}

final class FixedPasswordHasher implements UserPasswordHasherInterface
{
    public function hashPassword(\Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface $user, string $plainPassword): string { return 'hashed-'.$plainPassword; }
    public function isPasswordValid(\Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface $user, string $plainPassword): bool { return true; }
    public function needsRehash(\Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface $user): bool { return false; }
}

final class InMemoryUserRepository implements UserRepositoryInterface
{
    /** @var list<User> */
    private array $items = [];
    public function save(User $user): void { $this->items[] = $user; }
    public function findById(int $id): ?User { return $this->items[$id - 1] ?? null; }
    public function findByEmail(string $email): ?User { foreach ($this->items as $item) { if ($item->getEmail() === $email) { return $item; } } return null; }
    public function countAll(): int { return count($this->items); }
    public function listAll(?int $companyId = null): array { return $this->items; }
}

final class FixedCompanyRepository implements CompanyRepositoryInterface
{
    public function save(Company $company): void {}
    public function findById(int $id): ?Company { return new Company('Prosperium Company'); }
    public function countAll(): int { return 1; }
    public function listAll(?string $status = null): array { return [new Company('Prosperium Company')]; }
}

final class EmptyEmpresaRepository implements EmpresaRepositoryInterface
{
    public function save(\App\Company\Domain\Entity\Empresa $empresa): void {}
    public function findById(int $id): ?\App\Company\Domain\Entity\Empresa { return null; }
    public function existsByCompanyAndCnpj(int $companyId, string $cnpj): bool { return false; }
    public function listAll(?int $companyId = null, ?string $status = null): array { return []; }
}

final class EmptyUnidadeRepository implements UnidadeNegocioRepositoryInterface
{
    public function save(\App\Company\Domain\Entity\UnidadeNegocio $unidadeNegocio): void {}
    public function findById(int $id): ?\App\Company\Domain\Entity\UnidadeNegocio { return null; }
    public function existsByCompanyAndNome(int $companyId, string $nome): bool { return false; }
    public function existsByCompanyAndAbreviatura(int $companyId, string $abreviatura): bool { return false; }
    public function listAll(?int $companyId = null, ?string $status = null): array { return []; }
}

final class UserCompanyRepositorySpy implements UserCompanyRepositoryInterface
{
    public function save(\App\Identity\Domain\Entity\UserCompany $userCompany): void {}
    public function userHasCompany(int $userId, int $companyId): bool { return true; }
    public function isCompanyAdmin(int $userId, int $companyId): bool { return true; }
    public function listCompanyIdsByUser(int $userId): array { return [1]; }
}

final class UserEmpresaRepositorySpy implements UserEmpresaRepositoryInterface
{
    public function save(\App\Identity\Domain\Entity\UserEmpresa $userEmpresa): void {}
    public function userHasEmpresa(int $userId, int $companyId, int $empresaId): bool { return true; }
    public function listEmpresaIdsByUser(int $userId, ?int $companyId = null): array { return []; }
}

final class UserUnidadeRepositorySpy implements UserUnidadeRepositoryInterface
{
    public function save(\App\Identity\Domain\Entity\UserUnidade $userUnidade): void {}
    public function userHasUnidade(int $userId, int $companyId, int $unidadeId): bool { return true; }
    public function listUnidadeIdsByUser(int $userId, ?int $companyId = null): array { return []; }
}
