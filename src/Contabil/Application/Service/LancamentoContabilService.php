<?php
declare(strict_types=1);
namespace App\Contabil\Application\Service;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Contabil\Application\DTO\CreateLancamentoContabilRequest;
use App\Contabil\Domain\Entity\ContaFinanceiraSaldoDiario;
use App\Contabil\Domain\Entity\IndicadorFinanceiro;
use App\Contabil\Domain\Entity\LancamentoContabil;
use App\Contabil\Domain\Entity\LancamentoContabilItem;
use App\Contabil\Domain\Entity\SnapshotFluxoCaixa;
use App\Contabil\Domain\Repository\ContaContabilRepositoryInterface;
use App\Contabil\Domain\Repository\ContaFinanceiraSaldoDiarioRepositoryInterface;
use App\Contabil\Domain\Repository\IndicadorFinanceiroRepositoryInterface;
use App\Contabil\Domain\Repository\LancamentoContabilItemRepositoryInterface;
use App\Contabil\Domain\Repository\LancamentoContabilRepositoryInterface;
use App\Contabil\Domain\Repository\SnapshotFluxoCaixaRepositoryInterface;
use App\Financeiro\Domain\Repository\MovimentoFinanceiroRepositoryInterface;
use App\Financeiro\Domain\Repository\TituloRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class LancamentoContabilService
{
    public function __construct(private readonly LancamentoContabilRepositoryInterface $repo, private readonly LancamentoContabilItemRepositoryInterface $itemRepo, private readonly ContaContabilRepositoryInterface $contaRepo, private readonly ContaFinanceiraSaldoDiarioRepositoryInterface $saldoRepo, private readonly SnapshotFluxoCaixaRepositoryInterface $snapshotRepo, private readonly IndicadorFinanceiroRepositoryInterface $indicadorRepo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly TituloRepositoryInterface $tituloRepo, private readonly MovimentoFinanceiroRepositoryInterface $movimentoRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit) {}
    /** @return array{lancamento:LancamentoContabil,itens:list<LancamentoContabilItem>} */
    public function create(CreateLancamentoContabilRequest $r): array
    {
        $this->validator->validate($r);
        $company=$this->companyRepo->findById((int)$r->companyId); $empresa=$this->empresaRepo->findById((int)$r->empresaId); $unidade=$this->unidadeRepo->findById((int)$r->unidadeId); $titulo=$r->tituloId!==null?$this->tituloRepo->findById($r->tituloId):null;
        if(!$company||!$empresa||!$unidade){ throw new ResourceNotFoundException('Contexto contábil inválido.'); }
        if($empresa->getCompany()->getId()!==$company->getId()||$unidade->getCompany()->getId()!==$company->getId()){ throw new ValidationException(['contexto'=>['Company, empresa e unidade devem ser compatíveis.']]); }
        if($titulo!==null && ($titulo->getCompany()->getId()!==$company->getId()||$titulo->getEmpresa()->getId()!==$empresa->getId()||$titulo->getUnidade()->getId()!==$unidade->getId())){ throw new ValidationException(['tituloId'=>['Título fora do contexto informado.']]); }
        $debito=0.0; $credito=0.0; $contas=[];
        foreach($r->itens as $idx=>$item){ $conta=$this->contaRepo->findById((int)($item['contaContabilId']??0)); if($conta===null || $conta->getCompany()->getId()!==$company->getId()){ throw new ValidationException(['itens'=>[sprintf('Conta contábil inválida no item %d.', $idx+1)]]); } $natureza=(string)($item['natureza']??''); $valor=(float)($item['valor']??0); if(!in_array($natureza,['debito','credito'],true)){ throw new ValidationException(['itens'=>[sprintf('Natureza inválida no item %d.', $idx+1)]]); } $contas[]=['conta'=>$conta,'natureza'=>$natureza,'valor'=>number_format($valor,2,'.','')]; if($natureza==='debito'){$debito+=$valor;} else {$credito+=$valor;} }
        if(abs($debito-$credito)>0.00001){ throw new ValidationException(['itens'=>['Total de débitos deve ser igual ao total de créditos.']]); }
        return $this->tx->run(function() use($company,$empresa,$unidade,$titulo,$r,$contas): array {
            $data=new \DateTimeImmutable($r->dataLancamento);
            $lancamento=new LancamentoContabil($company,$empresa,$unidade,$titulo,$data,$r->historico);
            $this->repo->save($lancamento);
            $itens=[]; foreach($contas as $item){ $entity=new LancamentoContabilItem($lancamento,$item['conta'],$item['natureza'],$item['valor']); $this->itemRepo->save($entity); $itens[]=$entity; }
            $this->consolidarGerencial($company,$empresa,$unidade,$data);
            $this->audit->log((int)$company->getId(),'lancamento_contabil','contabil.lancamento_contabil.criado',['lancamentoContabilId'=>$lancamento->getId(),'itens'=>count($itens)]);
            return ['lancamento'=>$lancamento,'itens'=>$itens];
        });
    }
    private function consolidarGerencial($company,$empresa,$unidade,\DateTimeImmutable $data): void
    {
        $movimentos=$this->movimentoRepo->listAll((int)$company->getId(),(int)$empresa->getId(),(int)$unidade->getId());
        $saldo=0.0; $entradas=0.0; $saidas=0.0;
        foreach($movimentos as $movimento){ $valor=(float)$movimento->getValor(); if($movimento->getTipo()==='credito'){ $entradas+=$valor; $saldo+=$valor; } else { $saidas+=$valor; $saldo-=$valor; } }
        $saldoInicial=number_format($saldo-$entradas+$saidas,2,'.',''); $entradasFmt=number_format($entradas,2,'.',''); $saidasFmt=number_format($saidas,2,'.',''); $saldoFinal=number_format($saldo,2,'.','');
        $snapshotExistente=$this->snapshotRepo->findByContextAndDate((int)$company->getId(),(int)$empresa->getId(),(int)$unidade->getId(),$data);
        $snapshot=$snapshotExistente ?? new SnapshotFluxoCaixa($company,$empresa,$unidade,$data,$saldoInicial,$entradasFmt,$saidasFmt,$saldoFinal);
        if($snapshotExistente!==null){ $snapshot->atualizarValores($saldoInicial,$entradasFmt,$saidasFmt,$saldoFinal); }
        $this->snapshotRepo->save($snapshot);
        foreach($this->listarSaldosPorConta($movimentos,$company,$empresa,$unidade,$data) as $saldoConta){ $existente=$this->saldoRepo->findByContextContaAndDate((int)$company->getId(),(int)$empresa->getId(),(int)$unidade->getId(),(int)$saldoConta->getContaFinanceira()->getId(),$data); if($existente!==null){ $existente->atualizarSaldo($saldoConta->getSaldo()); $this->saldoRepo->save($existente); continue; } $this->saldoRepo->save($saldoConta); }
        $margem=$entradas > 0 ? (($entradas-$saidas)/$entradas)*100 : 0.0;
        $liquidez=$saidas > 0 ? $entradas / $saidas : ($entradas > 0 ? $entradas : 0.0);
        $this->indicadorRepo->save(new IndicadorFinanceiro($company,$empresa,$unidade,'margem_operacional','Margem Operacional',$data,number_format($margem,4,'.',''),['entradas'=>$entradas,'saidas'=>$saidas]));
        $this->indicadorRepo->save(new IndicadorFinanceiro($company,$empresa,$unidade,'liquidez_caixa','Liquidez de Caixa',$data,number_format($liquidez,4,'.',''),['entradas'=>$entradas,'saidas'=>$saidas]));
    }
    /** @return list<ContaFinanceiraSaldoDiario> */
    private function listarSaldosPorConta(array $movimentos,$company,$empresa,$unidade,\DateTimeImmutable $data): array
    {
        $totais=[]; foreach($movimentos as $movimento){ $contaId=(int)$movimento->getContaFinanceira()->getId(); $totais[$contaId]['conta']=$movimento->getContaFinanceira(); $totais[$contaId]['saldo']=($totais[$contaId]['saldo']??0.0)+($movimento->getTipo()==='credito'?(float)$movimento->getValor():-(float)$movimento->getValor()); }
        $saldos=[]; foreach($totais as $item){ $saldos[] = new ContaFinanceiraSaldoDiario($company,$empresa,$unidade,$item['conta'],$data,number_format((float)$item['saldo'],2,'.','')); }
        return $saldos;
    }
}
