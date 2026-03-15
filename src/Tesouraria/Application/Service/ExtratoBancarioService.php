<?php
declare(strict_types=1);
namespace App\Tesouraria\Application\Service;
use App\Cadastro\Domain\Repository\ContaFinanceiraRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Financeiro\Domain\Repository\MovimentoFinanceiroRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use App\Tesouraria\Application\DTO\ImportarExtratoRequest;
use App\Tesouraria\Domain\Entity\ExtratoBancario;
use App\Tesouraria\Domain\Repository\ConciliacaoRegraRepositoryInterface;
use App\Tesouraria\Domain\Repository\ExtratoBancarioRepositoryInterface;
use App\Tesouraria\Domain\Service\SugestaoConciliacaoService;
final class ExtratoBancarioService
{
    public function __construct(private readonly ExtratoBancarioRepositoryInterface $repo, private readonly ConciliacaoRegraRepositoryInterface $regrasRepo, private readonly MovimentoFinanceiroRepositoryInterface $movimentoRepo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly ContaFinanceiraRepositoryInterface $contaRepo, private readonly SugestaoConciliacaoService $sugestaoService, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit) {}
    /** @return list<ExtratoBancario> */
    public function importar(ImportarExtratoRequest $r): array
    {
        $this->validator->validate($r);
        $company=$this->companyRepo->findById((int)$r->companyId); $empresa=$this->empresaRepo->findById((int)$r->empresaId); $unidade=$this->unidadeRepo->findById((int)$r->unidadeId); $conta=$this->contaRepo->findById((int)$r->contaFinanceiraId);
        if(!$company||!$empresa||!$unidade||!$conta){ throw new ResourceNotFoundException('Contexto de importação inválido.'); }
        if($empresa->getCompany()->getId()!==$company->getId()||$unidade->getCompany()->getId()!==$company->getId()||$conta->getCompany()->getId()!==$company->getId()||$conta->getEmpresa()->getId()!==$empresa->getId()){ throw new ValidationException(['contexto'=>['Company, empresa, unidade e conta financeira devem ser coerentes.']]); }
        return $this->tx->run(function() use($r,$company,$empresa,$unidade,$conta): array { $items=[]; foreach($r->itens as $item){ $extrato=new ExtratoBancario($company,$empresa,$unidade,$conta,isset($item['codigoExterno'])?(string)$item['codigoExterno']:null,new \DateTimeImmutable($item['dataMovimento']),number_format((float)$item['valor'],2,'.',''),(string)$item['tipo'],(string)$item['descricao']); $this->repo->save($extrato); $items[]=$extrato; } $this->audit->log((int)$company->getId(),'extrato_bancario','tesouraria.extrato.importado',['quantidade'=>count($items),'contaFinanceiraId'=>$conta->getId()]); return $items; });
    }
    /** @return list<ExtratoBancario> */
    public function list(int $companyId, ?int $empresaId=null, ?int $unidadeId=null, ?int $contaFinanceiraId=null, ?string $status=null): array { return $this->repo->listAll($companyId,$empresaId,$unidadeId,$contaFinanceiraId,$status); }
    public function sugerir(ExtratoBancario $extrato): array { $regras=$this->regrasRepo->listActiveByCompany((int)$extrato->getCompany()->getId()); $movimentos=$this->movimentoRepo->listAll((int)$extrato->getCompany()->getId(),(int)$extrato->getEmpresa()->getId(),(int)$extrato->getUnidade()->getId(),null,(int)$extrato->getContaFinanceira()->getId()); return $this->sugestaoService->sugerir($extrato,$regras,$movimentos); }
}
