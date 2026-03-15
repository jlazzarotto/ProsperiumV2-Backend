<?php
declare(strict_types=1);
namespace App\Contabil\Application\Service;
use App\Contabil\Domain\Repository\DreGrupoRepositoryInterface;
use App\Contabil\Domain\Repository\DreMapeamentoCategoriaRepositoryInterface;
use App\Contabil\Domain\Repository\IndicadorFinanceiroRepositoryInterface;
use App\Contabil\Domain\Repository\SnapshotFluxoCaixaRepositoryInterface;
use App\Financeiro\Domain\Repository\MovimentoFinanceiroRepositoryInterface;
final class BIService
{
    public function __construct(private readonly DreGrupoRepositoryInterface $dreGrupoRepo, private readonly DreMapeamentoCategoriaRepositoryInterface $mapeamentoRepo, private readonly IndicadorFinanceiroRepositoryInterface $indicadorRepo, private readonly SnapshotFluxoCaixaRepositoryInterface $snapshotRepo, private readonly MovimentoFinanceiroRepositoryInterface $movimentoRepo) {}
    /** @return array{grupos:list<array<string,mixed>>,snapshot:?array<string,mixed>} */
    public function dre(int $companyId, int $empresaId, int $unidadeId, \DateTimeImmutable $data): array
    {
        $grupos=$this->dreGrupoRepo->listAll($companyId,'active');
        $mapeamentos=$this->mapeamentoRepo->findByCompanyId($companyId);
        $movimentos=$this->movimentoRepo->listAll($companyId,$empresaId,$unidadeId);
        $entradas=0.0; $saidas=0.0; foreach($movimentos as $m){ if($m->getTipo()==='credito'){$entradas+=(float)$m->getValor();} else {$saidas+=(float)$m->getValor();} }
        $snapshot=$this->snapshotRepo->findByContextAndDate($companyId,$empresaId,$unidadeId,$data);
        $items=[]; foreach($grupos as $grupo){ $tipo=strtolower($grupo->getCodigo()); $valor=match(true){ str_contains($tipo,'receita') => $entradas, str_contains($tipo,'despesa') => $saidas, str_contains($tipo,'resultado') => $entradas-$saidas, default => 0.0 }; $items[]=['id'=>$grupo->getId(),'codigo'=>$grupo->getCodigo(),'nome'=>$grupo->getNome(),'ordem'=>$grupo->getOrdem(),'valor'=>number_format($valor,2,'.',''),'mapeamentos'=>count(array_filter($mapeamentos, static fn($m): bool => $m->getDreGrupo()->getId()===$grupo->getId()))]; }
        usort($items, static fn(array $a,array $b): int => $a['ordem'] <=> $b['ordem']);
        return ['grupos'=>$items,'snapshot'=>$snapshot!==null?['dataReferencia'=>$snapshot->getDataReferencia()->format('Y-m-d'),'saldoFinal'=>$snapshot->getSaldoFinal()]:null];
    }
    /** @return list<\App\Contabil\Domain\Entity\IndicadorFinanceiro> */
    public function indicadores(int $companyId, int $empresaId, int $unidadeId, \DateTimeImmutable $data): array { return $this->indicadorRepo->listByDate($companyId,$empresaId,$unidadeId,$data); }
}
