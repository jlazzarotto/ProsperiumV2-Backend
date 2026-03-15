<?php
declare(strict_types=1);
namespace App\Tests\Cadastro\Application\Service;
use App\Cadastro\Application\DTO\CreatePessoaRequest;
use App\Cadastro\Application\Service\PessoaService;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Cadastro\Domain\Repository\PessoaRepositoryInterface;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
final class PessoaServiceTest extends TestCase
{
    public function testCreatePessoaPersistsForCompany(): void
    {
        $repo=new PessoaRepoSpy();
        $service=new PessoaService($repo,new FixedCompanyRepo(),new EmptyEmpresaRepo(),new RequestValidator(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),new TxCadastro(),new AuditoriaLogger($this->createMock(Connection::class)));
        $r=new CreatePessoaRequest(); $r->companyId=1; $r->nome='Fornecedor X'; $r->classificacao='fornecedor';
        $p=$service->create($r);
        self::assertSame('Fornecedor X',$p->getNome());
        self::assertCount(1,$repo->items);
    }
}
final class TxCadastro implements TransactionRunnerInterface { public function run(callable $operation): mixed { return $operation(); } }
final class PessoaRepoSpy implements PessoaRepositoryInterface { public array $items=[]; public function save(Pessoa $pessoa): void {$this->items[]=$pessoa;} public function findById(int $id): ?Pessoa {return $this->items[$id-1]??null;} public function listAll(int $companyId, ?int $empresaId = null, ?string $status = null): array {return $this->items;} }
final class FixedCompanyRepo implements CompanyRepositoryInterface { public function save(Company $company): void {} public function findById(int $id): ?Company { return new Company('Prosperium'); } public function countAll(): int { return 1; } public function listAll(?string $status = null): array { return [new Company('Prosperium')]; } }
final class EmptyEmpresaRepo implements EmpresaRepositoryInterface { public function save(\App\Company\Domain\Entity\Empresa $empresa): void {} public function findById(int $id): ?\App\Company\Domain\Entity\Empresa { return null; } public function existsByCompanyAndCnpj(int $companyId, string $cnpj): bool { return false; } public function listAll(?int $companyId = null, ?string $status = null): array { return []; } }
