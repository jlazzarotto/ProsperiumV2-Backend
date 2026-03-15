<?php
declare(strict_types=1);
namespace App\Cadastro\Application\Service;
use App\Cadastro\Application\DTO\CreateContaFinanceiraRequest;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Cadastro\Domain\Repository\ContaFinanceiraRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class ContaFinanceiraService {
    public function __construct(private readonly ContaFinanceiraRepositoryInterface $repo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit) {}
    public function create(CreateContaFinanceiraRequest $r): ContaFinanceira { $this->validator->validate($r); $company=$this->companyRepo->findById((int)$r->companyId); if(!$company){throw new ResourceNotFoundException('Company não encontrada.');} $empresa=$this->empresaRepo->findById((int)$r->empresaId); if($empresa===null||$empresa->getCompany()->getId()!==$company->getId()){throw new ValidationException(['empresaId'=>['Empresa inválida para a company informada.']]);} $unidade=null; if($r->unidadeId!==null){$unidade=$this->unidadeRepo->findById($r->unidadeId); if($unidade===null||$unidade->getCompany()->getId()!==$company->getId()){throw new ValidationException(['unidadeId'=>['Unidade inválida para a company informada.']]);}} return $this->tx->run(function() use($r,$company,$empresa,$unidade): ContaFinanceira { $c=new ContaFinanceira($company,$empresa,$unidade,$r->codigo,$r->nome,$r->tipo,$r->status); $this->repo->save($c); $this->audit->log((int)$company->getId(),'conta_financeira','cadastro.conta_financeira.created',['contaFinanceiraId'=>$c->getId()]); return $c;}); }
    public function list(int $companyId, ?int $empresaId=null, ?string $status=null): array { return $this->repo->listAll($companyId,$empresaId,$status); }
}
