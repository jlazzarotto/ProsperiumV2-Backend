<?php

declare(strict_types=1);

namespace App\Tests\Company\Application\Service;

use App\Company\Application\DTO\CreateEmpresaRequest;
use App\Company\Application\Service\EmpresaService;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Service\CnpjNormalizer;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class EmpresaServiceTest extends TestCase
{
    public function testCreateEmpresaNormalizesCnpj(): void
    {
        $company = new Company('Prosperium Company');
        $companyRepository = new SingleCompanyRepository($company);
        $empresaRepository = new InMemoryEmpresaRepository();

        $service = new EmpresaService(
            $empresaRepository,
            $companyRepository,
            new CnpjNormalizer(),
            new RequestValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
            new ImmediateTransactionRunnerEmpresa(),
            new AuditoriaLogger($this->createMock(Connection::class))
        );

        $request = new CreateEmpresaRequest();
        $request->companyId = 1;
        $request->razaoSocial = 'Empresa Fiscal';
        $request->nomeFantasia = 'Empresa';
        $request->cnpj = '12.345.678/0001-99';
        $request->status = 'active';

        $empresa = $service->create($request);

        self::assertSame('12345678000199', $empresa->getCnpj());
        self::assertCount(1, $empresaRepository->all());
    }

    public function testCreateEmpresaRequiresExistingCompany(): void
    {
        $service = new EmpresaService(
            new InMemoryEmpresaRepository(),
            new SingleCompanyRepository(null),
            new CnpjNormalizer(),
            new RequestValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
            new ImmediateTransactionRunnerEmpresa(),
            new AuditoriaLogger($this->createMock(Connection::class))
        );

        $request = new CreateEmpresaRequest();
        $request->companyId = 999;
        $request->razaoSocial = 'Empresa Fiscal';
        $request->cnpj = '12345678000199';
        $request->status = 'active';

        $this->expectException(ResourceNotFoundException::class);
        $service->create($request);
    }
}

final class ImmediateTransactionRunnerEmpresa implements TransactionRunnerInterface
{
    public function run(callable $operation): mixed
    {
        return $operation();
    }
}

final class SingleCompanyRepository implements CompanyRepositoryInterface
{
    public function __construct(private readonly ?Company $company)
    {
    }

    public function save(Company $company): void
    {
    }

    public function findById(int $id): ?Company
    {
        return $this->company;
    }

    public function countAll(): int
    {
        return $this->company !== null ? 1 : 0;
    }

    public function listAll(?string $status = null): array
    {
        return $this->company !== null ? [$this->company] : [];
    }
}

final class InMemoryEmpresaRepository implements EmpresaRepositoryInterface
{
    /** @var list<Empresa> */
    private array $items = [];

    public function save(Empresa $empresa): void
    {
        $this->items[] = $empresa;
    }

    public function findById(int $id): ?Empresa
    {
        return $this->items[$id - 1] ?? null;
    }

    public function existsByCompanyAndCnpj(int $companyId, string $cnpj): bool
    {
        foreach ($this->items as $empresa) {
            if ($empresa->getCnpj() === $cnpj) {
                return true;
            }
        }

        return false;
    }

    public function listAll(?int $companyId = null, ?string $status = null): array
    {
        return $this->items;
    }

    /**
     * @return list<Empresa>
     */
    public function all(): array
    {
        return $this->items;
    }
}
