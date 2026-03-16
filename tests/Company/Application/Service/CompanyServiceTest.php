<?php

declare(strict_types=1);

namespace App\Tests\Company\Application\Service;

use App\Company\Application\DTO\CreateCompanyRequest;
use App\Company\Application\Service\CompanyService;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\TenantInstance;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\TenantInstanceRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TenantDatabaseRegistryInterface;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class CompanyServiceTest extends TestCase
{
    public function testCreateCompanyCreatesCompanyAndTenantInstance(): void
    {
        $companyRepository = new InMemoryCompanyRepository();
        $tenantRepository = new InMemoryTenantInstanceRepository();
        $service = new CompanyService(
            $companyRepository,
            $tenantRepository,
            new InMemoryTenantDatabaseRegistry(['prosperium_holding']),
            self::createValidator(),
            new ImmediateTransactionRunner(),
            new AuditoriaLogger($this->createMock(Connection::class))
        );

        $request = new CreateCompanyRequest();
        $request->nome = 'Prosperium Holding';
        $request->tenancyMode = 'shared';
        $request->databaseKey = 'prosperium_holding';
        $request->status = 'active';

        $result = $service->create($request);

        self::assertSame('Prosperium Holding', $result['company']->getNome());
        self::assertSame('shared', $result['tenantInstance']->getTenancyMode());
        self::assertCount(1, $companyRepository->all());
        self::assertCount(1, $tenantRepository->all());
    }

    public function testCreateCompanyRejectsDuplicatedDatabaseKey(): void
    {
        $tenantRepository = new InMemoryTenantInstanceRepository();
        $tenantRepository->seedDatabaseKey('duplicated_key');

        $service = new CompanyService(
            new InMemoryCompanyRepository(),
            $tenantRepository,
            new InMemoryTenantDatabaseRegistry(['duplicated_key']),
            self::createValidator(),
            new ImmediateTransactionRunner(),
            new AuditoriaLogger($this->createMock(Connection::class))
        );

        $request = new CreateCompanyRequest();
        $request->nome = 'Prosperium Duplicate';
        $request->tenancyMode = 'shared';
        $request->databaseKey = 'duplicated_key';
        $request->status = 'active';

        $this->expectException(ValidationException::class);
        $service->create($request);
    }

    public function testCreateCompanyRejectsDatabaseKeyMissingInRegistry(): void
    {
        $service = new CompanyService(
            new InMemoryCompanyRepository(),
            new InMemoryTenantInstanceRepository(),
            new InMemoryTenantDatabaseRegistry(['shared_base_1']),
            self::createValidator(),
            new ImmediateTransactionRunner(),
            new AuditoriaLogger($this->createMock(Connection::class))
        );

        $request = new CreateCompanyRequest();
        $request->nome = 'Prosperium Missing Registry';
        $request->tenancyMode = 'shared';
        $request->databaseKey = 'shared_base_2';
        $request->status = 'active';

        $this->expectException(ValidationException::class);
        $service->create($request);
    }

    private static function createValidator(): RequestValidator
    {
        return new RequestValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator());
    }
}

final class ImmediateTransactionRunner implements TransactionRunnerInterface
{
    public function run(callable $operation): mixed
    {
        return $operation();
    }
}

final class InMemoryCompanyRepository implements CompanyRepositoryInterface
{
    /** @var list<Company> */
    private array $items = [];

    public function save(Company $company): void
    {
        $this->items[] = $company;
    }

    public function findById(int $id): ?Company
    {
        return $this->items[$id - 1] ?? null;
    }

    public function listAll(?string $status = null): array
    {
        return $this->items;
    }

    public function countAll(): int
    {
        return count($this->items);
    }

    /**
     * @return list<Company>
     */
    public function all(): array
    {
        return $this->items;
    }
}

final class InMemoryTenantInstanceRepository implements TenantInstanceRepositoryInterface
{
    /** @var list<TenantInstance> */
    private array $items = [];

    /** @var list<string> */
    private array $databaseKeys = [];

    public function save(TenantInstance $tenantInstance): void
    {
        $this->items[] = $tenantInstance;
        $this->databaseKeys[] = $tenantInstance->getDatabaseKey();
    }

    public function findByCompanyId(int $companyId): ?TenantInstance
    {
        return $this->items[$companyId - 1] ?? null;
    }

    public function existsByDatabaseKey(string $databaseKey): bool
    {
        return in_array($databaseKey, $this->databaseKeys, true);
    }

    public function findByDatabaseKey(string $databaseKey): ?TenantInstance
    {
        foreach ($this->items as $tenantInstance) {
            if ($tenantInstance->getDatabaseKey() === $databaseKey) {
                return $tenantInstance;
            }
        }

        return null;
    }

    public function seedDatabaseKey(string $databaseKey): void
    {
        $this->databaseKeys[] = $databaseKey;
    }

    /**
     * @return list<TenantInstance>
     */
    public function all(): array
    {
        return $this->items;
    }
}

final class InMemoryTenantDatabaseRegistry implements TenantDatabaseRegistryInterface
{
    /**
     * @param list<string> $keys
     */
    public function __construct(private readonly array $keys)
    {
    }

    public function hasDatabaseKey(string $databaseKey): bool
    {
        return in_array(trim($databaseKey), $this->keys, true);
    }

    public function findDatabaseUrl(string $databaseKey): ?string
    {
        return $this->hasDatabaseKey($databaseKey) ? sprintf('mysql://tenant/%s', trim($databaseKey)) : null;
    }
}
