<?php
declare(strict_types=1);
namespace App\Tesouraria\Application\Service;
use App\Cadastro\Domain\Repository\ContaFinanceiraRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use App\Tesouraria\Application\DTO\CreateConciliacaoRegraRequest;
use App\Tesouraria\Domain\Entity\ConciliacaoRegra;
use App\Tesouraria\Domain\Repository\ConciliacaoRegraRepositoryInterface;
final class ConciliacaoRegraService
{
    public function __construct(private readonly ConciliacaoRegraRepositoryInterface $repo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly ContaFinanceiraRepositoryInterface $contaRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit) {}
    public function create(CreateConciliacaoRegraRequest $r): ConciliacaoRegra
    {
        $this->validator->validate($r); $company=$this->companyRepo->findById((int)$r->companyId); if(!$company){ throw new ResourceNotFoundException('Company não encontrada.'); }
        $empresa=$r->empresaId!==null ? $this->empresaRepo->findById($r->empresaId) : null; $unidade=$r->unidadeId!==null ? $this->unidadeRepo->findById($r->unidadeId) : null; $conta=$r->contaFinanceiraId!==null ? $this->contaRepo->findById($r->contaFinanceiraId) : null;
        if(($empresa && $empresa->getCompany()->getId()!==$company->getId())||($unidade && $unidade->getCompany()->getId()!==$company->getId())||($conta && $conta->getCompany()->getId()!==$company->getId())){ throw new ValidationException(['contexto'=>['Empresa, unidade e conta devem pertencer à mesma company.']]); }
        return $this->tx->run(function() use($r,$company,$empresa,$unidade,$conta): ConciliacaoRegra { $regra=new ConciliacaoRegra($company,$empresa,$unidade,$conta,$r->descricaoContains,$r->tipoMovimentoSugerido,$r->aplicacao,$r->status); $this->repo->save($regra); $this->audit->log((int)$company->getId(),'conciliacao_regra','tesouraria.conciliacao_regra.criada',['regraId'=>$regra->getId()]); return $regra; });
    }
}
