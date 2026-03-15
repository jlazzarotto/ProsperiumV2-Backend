<?php

declare(strict_types=1);

namespace App\Tests\Identity\Application\Service;

use App\Identity\Application\Security\PermissionContext;
use App\Identity\Application\Service\AuthorizationService;
use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Identity\Domain\Repository\UserEmpresaRepositoryInterface;
use App\Identity\Domain\Repository\UserPerfilRepositoryInterface;
use App\Identity\Domain\Repository\UserUnidadeRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class AuthorizationServiceTest extends TestCase
{
    public function testAllowsWhenPermissionAndScopesMatch(): void
    {
        $user = new User('Admin', 'admin@example.com', 'hash');

        $service = new AuthorizationService(
            new AuthorizationUserCompanyRepository(true),
            new AuthorizationUserEmpresaRepository(true),
            new AuthorizationUserUnidadeRepository(true),
            new AuthorizationUserPerfilRepository(true)
        );

        $this->forceId($user, 1);

        self::assertTrue($service->can($user, new PermissionContext('identity.users.view', 1, 10, 20)));
    }

    public function testDeniesWhenUserDoesNotBelongToCompany(): void
    {
        $user = new User('Admin', 'admin@example.com', 'hash');
        $this->forceId($user, 1);

        $service = new AuthorizationService(
            new AuthorizationUserCompanyRepository(false),
            new AuthorizationUserEmpresaRepository(true),
            new AuthorizationUserUnidadeRepository(true),
            new AuthorizationUserPerfilRepository(true)
        );

        self::assertFalse($service->can($user, new PermissionContext('identity.users.view', 1)));
    }

    private function forceId(User $user, int $id): void
    {
        $reflection = new \ReflectionProperty($user, 'id');
        $reflection->setValue($user, $id);
    }
}

final class AuthorizationUserCompanyRepository implements UserCompanyRepositoryInterface
{
    public function __construct(private readonly bool $allowed)
    {
    }

    public function save(\App\Identity\Domain\Entity\UserCompany $userCompany): void {}
    public function userHasCompany(int $userId, int $companyId): bool { return $this->allowed; }
    public function isCompanyAdmin(int $userId, int $companyId): bool { return $this->allowed; }
    public function listCompanyIdsByUser(int $userId): array { return []; }
}

final class AuthorizationUserEmpresaRepository implements UserEmpresaRepositoryInterface
{
    public function __construct(private readonly bool $allowed)
    {
    }

    public function save(\App\Identity\Domain\Entity\UserEmpresa $userEmpresa): void {}
    public function userHasEmpresa(int $userId, int $companyId, int $empresaId): bool { return $this->allowed; }
    public function listEmpresaIdsByUser(int $userId, ?int $companyId = null): array { return []; }
}

final class AuthorizationUserUnidadeRepository implements UserUnidadeRepositoryInterface
{
    public function __construct(private readonly bool $allowed)
    {
    }

    public function save(\App\Identity\Domain\Entity\UserUnidade $userUnidade): void {}
    public function userHasUnidade(int $userId, int $companyId, int $unidadeId): bool { return $this->allowed; }
    public function listUnidadeIdsByUser(int $userId, ?int $companyId = null): array { return []; }
}

final class AuthorizationUserPerfilRepository implements UserPerfilRepositoryInterface
{
    public function __construct(private readonly bool $allowed)
    {
    }

    public function save(\App\Identity\Domain\Entity\UserPerfil $userPerfil): void {}
    public function userHasPermission(int $userId, string $permissionCode, ?int $companyId = null, ?int $empresaId = null, ?int $unidadeId = null): bool { return $this->allowed; }
    public function listProfileCodesByUser(int $userId, ?int $companyId = null): array { return []; }
}
