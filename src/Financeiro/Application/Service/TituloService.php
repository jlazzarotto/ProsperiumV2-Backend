<?php
declare(strict_types=1);
namespace App\Financeiro\Application\Service;
use App\Cadastro\Domain\Repository\ContaFinanceiraRepositoryInterface;
use App\Cadastro\Domain\Repository\PessoaRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Financeiro\Application\DTO\CreateTituloRequest;
use App\Financeiro\Application\DTO\ParcelarTituloRequest;
use App\Financeiro\Domain\Entity\Titulo;
use App\Financeiro\Domain\Event\TituloCriado;
use App\Financeiro\Domain\Repository\AnexoFinanceiroRepositoryInterface;
use App\Financeiro\Domain\Repository\TituloParcelaRepositoryInterface;
use App\Financeiro\Domain\Repository\TituloRepositoryInterface;
use App\Financeiro\Domain\Service\ParcelamentoService;
use App\Shared\Application\Bus\EventBusInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class TituloService
{
    public function __construct(private readonly TituloRepositoryInterface $repo, private readonly TituloParcelaRepositoryInterface $parcelaRepo, private readonly AnexoFinanceiroRepositoryInterface $anexoRepo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly PessoaRepositoryInterface $pessoaRepo, private readonly ContaFinanceiraRepositoryInterface $contaRepo, private readonly ParcelamentoService $parcelamentoService, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit, private readonly EventBusInterface $eventBus) {}

    public function create(CreateTituloRequest $r): Titulo
    {
        $this->validator->validate($r);
        [$company,$empresa,$unidade,$pessoa,$conta] = $this->resolveContext((int)$r->companyId,(int)$r->empresaId,(int)$r->unidadeId,(int)$r->pessoaId,$r->contaFinanceiraId);
        return $this->tx->run(function () use ($r,$company,$empresa,$unidade,$pessoa,$conta): Titulo {
            $titulo = new Titulo($company,$empresa,$unidade,$pessoa,$r->tipo,$r->numeroDocumento,number_format((float)$r->valorTotal,2,'.',''),new \DateTimeImmutable($r->dataEmissao),$r->observacoes,$conta);
            $this->repo->save($titulo);
            $vencimento = new \DateTimeImmutable($r->primeiroVencimento ?? $r->dataEmissao);
            $parcela = $this->parcelamentoService->gerarParcelas($titulo,[['numero'=>1,'valor'=>number_format((float)$r->valorTotal,2,'.',''),'vencimento'=>$vencimento]])[0];
            $this->parcelaRepo->save($parcela);
            $this->audit->log((int)$company->getId(),'titulo','financeiro.titulo.created',['tituloId'=>$titulo->getId()]);
            $this->eventBus->publish(new TituloCriado((int)$titulo->getId(),(int)$company->getId()));
            return $titulo;
        });
    }

    /** @return list<Titulo> */
    public function list(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $tipo = null, ?string $status = null): array { return $this->repo->listAll($companyId,$empresaId,$unidadeId,$tipo,$status); }

    /** @return array{titulo:Titulo, parcelas:array, anexos:array} */
    public function getById(int $id): array { $titulo=$this->repo->findById($id); if($titulo===null){throw new ResourceNotFoundException('Título não encontrado.');} return ['titulo'=>$titulo,'parcelas'=>$this->parcelaRepo->findByTituloId($id),'anexos'=>$this->anexoRepo->findByTituloId($id)]; }

    /** @return list<\App\Financeiro\Domain\Entity\TituloParcela> */
    public function parcelar(int $tituloId, ParcelarTituloRequest $r): array
    {
        $titulo=$this->repo->findById($tituloId); if($titulo===null){throw new ResourceNotFoundException('Título não encontrado.');}
        $parcelasExistentes=$this->parcelaRepo->findByTituloId($tituloId);
        foreach($parcelasExistentes as $parcela){ if((float)$parcela->getValorAberto() < (float)$parcela->getValor()){ throw new ValidationException(['parcelas'=>['Não é permitido re-parcelar título com baixa realizada.']]); } }
        $parcelasData=[]; foreach($r->parcelas as $item){ $parcelasData[]=['numero'=>(int)$item['numero'],'valor'=>number_format((float)$item['valor'],2,'.',''),'vencimento'=>new \DateTimeImmutable($item['vencimento'])]; }
        return $this->tx->run(function() use($titulo,$tituloId,$parcelasData): array { $this->parcelaRepo->removeByTituloId($tituloId); $parcelas=$this->parcelamentoService->gerarParcelas($titulo,$parcelasData); foreach($parcelas as $p){ $this->parcelaRepo->save($p);} $this->audit->log((int)$titulo->getCompany()->getId(),'titulo','financeiro.titulo.parcelado',['tituloId'=>$titulo->getId(),'parcelas'=>count($parcelas)]); return $parcelas; });
    }

    private function resolveContext(int $companyId, int $empresaId, int $unidadeId, int $pessoaId, ?int $contaId): array
    {
        $company=$this->companyRepo->findById($companyId); $empresa=$this->empresaRepo->findById($empresaId); $unidade=$this->unidadeRepo->findById($unidadeId); $pessoa=$this->pessoaRepo->findById($pessoaId);
        if(!$company||!$empresa||!$unidade||!$pessoa){ throw new ResourceNotFoundException('Contexto financeiro inválido.'); }
        if($empresa->getCompany()->getId()!==$company->getId()||$unidade->getCompany()->getId()!==$company->getId()||$pessoa->getCompany()->getId()!==$company->getId()){ throw new ValidationException(['contexto'=>['Empresa, unidade e pessoa devem pertencer à mesma company.']]); }
        $conta=null; if($contaId!==null){ $conta=$this->contaRepo->findById($contaId); if($conta===null||$conta->getCompany()->getId()!==$company->getId()||$conta->getEmpresa()->getId()!==$empresa->getId()){ throw new ValidationException(['contaFinanceiraId'=>['Conta financeira inválida para a company/empresa informada.']]); } }
        return [$company,$empresa,$unidade,$pessoa,$conta];
    }
}
