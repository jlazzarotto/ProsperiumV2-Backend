<?php

declare(strict_types=1);

namespace App\Tests\Identity\Application\Service;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Identity\Application\DTO\CreatePerfilRequest;
use App\Identity\Application\Service\PerfilService;
use App\Identity\Domain\Entity\Perfil;
use App\Identity\Domain\Entity\Permissao;
use App\Identity\Domain\Repository\PerfilPermissaoRepositoryInterface;
use App\Identity\Domain\Repository\PerfilRepositoryInterface;
use App\Identity\Domain\Repository\PermissaoRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class PerfilServiceTest extends TestCase
{
    public function testCreatePerfilPersistsProfile(): void
    {
        $service = new PerfilService(
            new PerfilRepositorySpy(),
            new PerfilPermissaoRepositorySpy(),
            new PermissaoRepositorySpy(),
            new CompanyRepositoryPerfil(),
            new RequestValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
            new ImmediateTransactionRunnerPerfil(),
            new AuditoriaLogger($this->createMock(Connection::class))
        );

        $request = new CreatePerfilRequest();
        $request->companyId = 1;
        $request->codigo = 'financeiro_analista';
        $request->nome = 'Financeiro Analista';
        $request->permissionCodes = ['identity.users.view'];

        $perfil = $service->create($request);

        self::assertSame('financeiro_analista', $perfil->getCodigo());
    }
}

final class ImmediateTransactionRunnerPerfil implements TransactionRunnerInterface
{
    public function run(callable $operation): mixed { return $operation(); }
}

final class CompanyRepositoryPerfil implements CompanyRepositoryInterface
{
    public function save(Company $company): void {}
    public function findById(int $id): ?Company { return new Company('Prosperium Company'); }
    public function countAll(): int { return 1; }
    public function listAll(?string $status = null): array { return [new Company('Prosperium Company')]; }
}

final class PerfilRepositorySpy implements PerfilRepositoryInterface
{
    public function save(Perfil $perfil): void {}
    public function findByCodigo(string $codigo, ?int $companyId = null): ?Perfil { return null; }
    public function findByCodigos(array $codigos, ?int $companyId = null): array { return []; }
}

final class PerfilPermissaoRepositorySpy implements PerfilPermissaoRepositoryInterface
{
    public function save(\App\Identity\Domain\Entity\PerfilPermissao $perfilPermissao): void {}
}

final class PermissaoRepositorySpy implements PermissaoRepositoryInterface
{
    public function findByCodigo(string $codigo): ?Permissao { return null; }
    public function findByCodigos(array $codigos): array { return [(new \ReflectionClass(Permissao::class))->newInstanceWithoutConstructor()]; }
    public function listAll(?string $moduloCodigo = null): array { return []; }
}
