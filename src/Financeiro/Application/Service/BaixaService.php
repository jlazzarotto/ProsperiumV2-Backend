<?php
declare(strict_types=1);
namespace App\Financeiro\Application\Service;
use App\Cadastro\Domain\Repository\ContaFinanceiraRepositoryInterface;
use App\Financeiro\Application\DTO\BaixarParcelaRequest;
use App\Financeiro\Domain\Event\TituloBaixado;
use App\Financeiro\Domain\Repository\BaixaRepositoryInterface;
use App\Financeiro\Domain\Repository\MovimentoFinanceiroRepositoryInterface;
use App\Financeiro\Domain\Repository\TituloParcelaRepositoryInterface;
use App\Financeiro\Domain\Service\BaixaFinanceiraService;
use App\Identity\Domain\Repository\UserAlcadaRepositoryInterface;
use App\Shared\Application\Bus\EventBusInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\AuthenticatedUserProviderInterface;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class BaixaService
{
    public function __construct(private readonly TituloParcelaRepositoryInterface $parcelaRepo, private readonly BaixaRepositoryInterface $baixaRepo, private readonly MovimentoFinanceiroRepositoryInterface $movimentoRepo, private readonly ContaFinanceiraRepositoryInterface $contaRepo, private readonly BaixaFinanceiraService $baixaFinanceiraService, private readonly UserAlcadaRepositoryInterface $userAlcadaRepository, private readonly AuthenticatedUserProviderInterface $authenticatedUserProvider, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit, private readonly EventBusInterface $eventBus) {}
    /** @return array{baixa:\App\Financeiro\Domain\Entity\Baixa, parcela:\App\Financeiro\Domain\Entity\TituloParcela} */
    public function baixar(int $parcelaId, BaixarParcelaRequest $r): array
    {
        $this->validator->validate($r);
        $authenticatedUser = $this->authenticatedUserProvider->requireUser();
        $parcela=$this->parcelaRepo->findById($parcelaId); if($parcela===null){ throw new ResourceNotFoundException('Parcela não encontrada.'); }
        if($parcela->getCompanyId()!==$r->companyId){ throw new ValidationException(['companyId'=>['Parcela não pertence à company informada.']]); }
        $conta=$this->contaRepo->findById((int)$r->contaFinanceiraId); if($conta===null||$conta->getCompanyId()!==$parcela->getCompanyId()||$conta->getEmpresa()->getId()!==$parcela->getEmpresa()->getId()){ throw new ValidationException(['contaFinanceiraId'=>['Conta financeira inválida para a parcela.']]); }
        $tipoOperacao = sprintf('baixa_titulo_%s', $parcela->getTitulo()->getTipo());
        $valor = number_format((float) $r->valor, 2, '.', '');
        if(!$this->userAlcadaRepository->userHasActiveAlcadaForValue($authenticatedUser->id,(int)$parcela->getCompanyId(),(int)$parcela->getEmpresa()->getId(),(int)$parcela->getUnidade()->getId(),$tipoOperacao,$valor)){ throw new ValidationException(['alcada'=>[sprintf('Usuário autenticado não possui alçada para %s no valor %s.',$tipoOperacao,$valor)]]); }
        return $this->tx->run(function() use($parcela,$conta,$r): array {
            $result=$this->baixaFinanceiraService->baixar($parcela,$conta,number_format((float)$r->valor,2,'.',''),new \DateTimeImmutable($r->dataPagamento),$r->observacoes);
            $this->parcelaRepo->save($parcela);
            $this->baixaRepo->save($result['baixa']);
            $this->movimentoRepo->save($result['movimento']);
            $parcelas=$this->parcelaRepo->findByTituloId((int)$parcela->getTitulo()->getId());
            $status='liquidado';
            foreach($parcelas as $item){ if($item->getStatus()==='parcial'){ $status='parcial'; break; } if($item->getStatus()!=='liquidado'){ $status='aberto'; } }
            $parcela->getTitulo()->marcarStatus($status);
            $this->audit->log((int)$parcela->getCompanyId(),'titulo','financeiro.titulo.baixado',['tituloId'=>$parcela->getTitulo()->getId(),'parcelaId'=>$parcela->getId(),'baixaId'=>$result['baixa']->getId()]);
            $this->eventBus->publish(new TituloBaixado((int)$parcela->getTitulo()->getId(),(int)$parcela->getId(),(int)$parcela->getCompanyId()));
            return ['baixa'=>$result['baixa'],'parcela'=>$parcela];
        });
    }
}
