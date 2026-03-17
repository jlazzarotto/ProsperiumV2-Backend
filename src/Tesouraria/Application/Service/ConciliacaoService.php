<?php
declare(strict_types=1);
namespace App\Tesouraria\Application\Service;
use App\Financeiro\Domain\Repository\BaixaRepositoryInterface;
use App\Financeiro\Domain\Repository\MovimentoFinanceiroRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use App\Tesouraria\Application\DTO\CreateConciliacaoRequest;
use App\Tesouraria\Domain\Entity\ConciliacaoBancaria;
use App\Tesouraria\Domain\Repository\ConciliacaoBancariaRepositoryInterface;
use App\Tesouraria\Domain\Repository\ExtratoBancarioRepositoryInterface;
final class ConciliacaoService
{
    public function __construct(private readonly ConciliacaoBancariaRepositoryInterface $repo, private readonly ExtratoBancarioRepositoryInterface $extratoRepo, private readonly MovimentoFinanceiroRepositoryInterface $movimentoRepo, private readonly BaixaRepositoryInterface $baixaRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit) {}
    public function create(CreateConciliacaoRequest $r): ConciliacaoBancaria
    {
        $this->validator->validate($r);
        $extrato=$this->extratoRepo->findById((int)$r->extratoBancarioId); if($extrato===null){ throw new ResourceNotFoundException('Extrato bancário não encontrado.'); }
        if($extrato->getCompanyId()!==$r->companyId){ throw new ValidationException(['companyId'=>['Extrato bancário não pertence à company informada.']]); }
        $movimento=null; $baixa=null;
        if($r->movimentoFinanceiroId!==null){ $movimento=$this->movimentoRepo->findById($r->movimentoFinanceiroId); if($movimento===null||$movimento->getCompanyId()!==$extrato->getCompanyId()){ throw new ValidationException(['movimentoFinanceiroId'=>['Movimento financeiro inválido para o extrato.']]); } }
        if($r->baixaId!==null){ $baixa=$this->baixaRepo->findById($r->baixaId); if($baixa===null||$baixa->getCompanyId()!==$extrato->getCompanyId()){ throw new ValidationException(['baixaId'=>['Baixa inválida para o extrato.']]); } }
        return $this->tx->run(function() use($extrato,$movimento,$baixa,$r): ConciliacaoBancaria { $extrato->conciliar($movimento,$baixa); $this->extratoRepo->save($extrato); $conciliacao=new ConciliacaoBancaria($extrato->getCompanyId(),$extrato->getEmpresa(),$extrato->getUnidade(),$extrato,$movimento,$baixa,$r->modo); $this->repo->save($conciliacao); $this->audit->log((int)$extrato->getCompanyId(),'conciliacao_bancaria','tesouraria.conciliacao.criada',['conciliacaoId'=>$conciliacao->getId(),'extratoId'=>$extrato->getId()]); return $conciliacao; });
    }
    /** @return list<ConciliacaoBancaria> */ public function list(int $companyId, ?int $empresaId=null, ?int $unidadeId=null): array { return $this->repo->listAll($companyId,$empresaId,$unidadeId); }
}
