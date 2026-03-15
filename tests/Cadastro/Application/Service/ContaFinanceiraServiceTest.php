<?php
declare(strict_types=1);
namespace App\Tests\Cadastro\Application\Service;
use App\Cadastro\Application\DTO\CreateContaFinanceiraRequest;
use App\Cadastro\Application\Service\ContaFinanceiraService;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Cadastro\Domain\Repository\ContaFinanceiraRepositoryInterface;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
final class ContaFinanceiraServiceTest extends TestCase
{
    public function testCreateContaFinanceiraForEmpresa(): void
    {
        $repo=new ContaRepoSpy();
        $service=new ContaFinanceiraService($repo,new CompanyRepoConta(),new EmpresaRepoConta(),new UnidadeRepoConta(),new RequestValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),new TxCadastroConta(),new AuditoriaLogger($this->createMock(Connection::class)));
        $r=new CreateContaFinanceiraRequest(); $r->companyId=1; $r->empresaId=1; $r->codigo='CX001'; $r->nome='Caixa Matriz'; $r->tipo='caixa';
        $c=$service->create($r);
        self::assertSame('CX001',$c->getCodigo());
        self::assertCount(1,$repo->items);
    }
}
final class TxCadastroConta implements TransactionRunnerInterface { public function run(callable $operation): mixed { return $operation(); } }
final class ContaRepoSpy implements ContaFinanceiraRepositoryInterface { public array $items=[]; public function save(ContaFinanceira $conta): void {$this->items[]=$conta;} public function findById(int $id): ?ContaFinanceira { return $this->items[$id-1]??null; } public function listAll(int $companyId, ?int $empresaId = null, ?string $status = null): array { return $this->items; } }
final class CompanyRepoConta implements CompanyRepositoryInterface { public function save(Company $company): void {} public function findById(int $id): ?Company { return new Company('Prosperium'); } public function countAll(): int { return 1; } public function listAll(?string $status = null): array { return [new Company('Prosperium')]; } }
final class EmpresaRepoConta implements EmpresaRepositoryInterface { public function save(Empresa $empresa): void {} public function findById(int $id): ?Empresa { $company=new Company('Prosperium'); $ref=new \ReflectionClass(Empresa::class); return $ref->newInstanceArgs([$company,'Empresa A',null,'12345678000199','active']); } public function existsByCompanyAndCnpj(int $companyId, string $cnpj): bool { return false; } public function listAll(?int $companyId = null, ?string $status = null): array { return []; } }
final class UnidadeRepoConta implements UnidadeNegocioRepositoryInterface { public function save(\App\Company\Domain\Entity\UnidadeNegocio $unidadeNegocio): void {} public function findById(int $id): ?\App\Company\Domain\Entity\UnidadeNegocio { return null; } public function existsByCompanyAndNome(int $companyId, string $nome): bool { return false; } public function existsByCompanyAndAbreviatura(int $companyId, string $abreviatura): bool { return false; } public function listAll(?int $companyId = null, ?string $status = null): array { return []; } }
