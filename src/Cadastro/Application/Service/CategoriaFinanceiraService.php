<?php
declare(strict_types=1);
namespace App\Cadastro\Application\Service;
use App\Cadastro\Application\DTO\CreateCategoriaFinanceiraRequest;
use App\Cadastro\Domain\Entity\CategoriaFinanceira;
use App\Cadastro\Domain\Repository\CategoriaFinanceiraRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class CategoriaFinanceiraService {
    public function __construct(private readonly CategoriaFinanceiraRepositoryInterface $repo, private readonly CompanyRepositoryInterface $companyRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit) {}
    public function create(CreateCategoriaFinanceiraRequest $r): CategoriaFinanceira { $this->validator->validate($r); $company=$this->companyRepo->findById((int)$r->companyId); if(!$company){throw new ResourceNotFoundException('Company não encontrada.');} $parent=null; if($r->parentId!==null){$parent=$this->repo->findById($r->parentId); if($parent===null||$parent->getCompany()->getId()!==$company->getId()){throw new ValidationException(['parentId'=>['Categoria pai inválida para a company.']]);}} return $this->tx->run(function() use($r,$company,$parent): CategoriaFinanceira { $c=new CategoriaFinanceira($company,$parent,$r->codigo,$r->nome,$r->tipo,$r->status); $this->repo->save($c); $this->audit->log((int)$company->getId(),'categoria_financeira','cadastro.categoria.created',['categoriaId'=>$c->getId()]); return $c;}); }
    public function list(int $companyId, ?string $status=null): array { return $this->repo->listAll($companyId,$status); }
}
