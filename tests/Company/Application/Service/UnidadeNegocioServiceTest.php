<?php

declare(strict_types=1);

namespace App\Tests\Company\Application\Service;

use App\Company\Application\DTO\CreateUnidadeNegocioRequest;
use App\Company\Application\Service\UnidadeNegocioService;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class UnidadeNegocioServiceTest extends TestCase
{
    public function testCreateUnidadeNegocioPersistsRecord(): void
    {
        $service = new UnidadeNegocioService(
            new InMemoryUnidadeNegocioRepository(),
            new SingleCompanyRepositoryUnidade(new Company('Prosperium Company')),
            new RequestValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
            new ImmediateTransactionRunnerUnidade(),
            new AuditoriaLogger($this->createMock(Connection::class))
        );

        $request = new CreateUnidadeNegocioRequest();
        $request->companyId = 1;
        $request->nome = 'Operacao Sul';
        $request->abreviatura = 'SUL';
        $request->status = 'active';

        $unidade = $service->create($request);

        self::assertSame('Operacao Sul', $unidade->getNome());
        self::assertSame('SUL', $unidade->getAbreviatura());
    }

    public function testCreateUnidadeNegocioRejectsDuplicatedAbreviatura(): void
    {
        $repository = new InMemoryUnidadeNegocioRepository();
        $repository->seedAbreviatura('SUL');

        $service = new UnidadeNegocioService(
            $repository,
            new SingleCompanyRepositoryUnidade(new Company('Prosperium Company')),
            new RequestValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
            new ImmediateTransactionRunnerUnidade(),
            new AuditoriaLogger($this->createMock(Connection::class))
        );

        $request = new CreateUnidadeNegocioRequest();
        $request->companyId = 1;
        $request->nome = 'Operacao Sul';
        $request->abreviatura = 'SUL';
        $request->status = 'active';

        $this->expectException(ValidationException::class);
        $service->create($request);
    }
}

final class ImmediateTransactionRunnerUnidade implements TransactionRunnerInterface
{
    public function run(callable $operation): mixed
    {
        return $operation();
    }
}

final class SingleCompanyRepositoryUnidade implements CompanyRepositoryInterface
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

final class InMemoryUnidadeNegocioRepository implements UnidadeNegocioRepositoryInterface
{
    /** @var list<UnidadeNegocio> */
    private array $items = [];

    /** @var list<string> */
    private array $abreviaturas = [];

    public function save(UnidadeNegocio $unidadeNegocio): void
    {
        $this->items[] = $unidadeNegocio;
        $this->abreviaturas[] = $unidadeNegocio->getAbreviatura();
    }

    public function findById(int $id): ?UnidadeNegocio
    {
        return $this->items[$id - 1] ?? null;
    }

    public function existsByCompanyAndNome(int $companyId, string $nome): bool
    {
        foreach ($this->items as $item) {
            if ($item->getNome() === $nome) {
                return true;
            }
        }

        return false;
    }

    public function existsByCompanyAndAbreviatura(int $companyId, string $abreviatura): bool
    {
        return in_array($abreviatura, $this->abreviaturas, true);
    }

    public function listAll(?int $companyId = null, ?string $status = null): array
    {
        return $this->items;
    }

    public function seedAbreviatura(string $abreviatura): void
    {
        $this->abreviaturas[] = $abreviatura;
    }
}
