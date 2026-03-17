<?php
declare(strict_types=1);
namespace App\Cobranca\Application\Service;
use App\Cadastro\Domain\Repository\ContaFinanceiraRepositoryInterface;
use App\Cobranca\Application\DTO\CreateBoletoRemessaRequest;
use App\Cobranca\Domain\Entity\BoletoRemessa;
use App\Cobranca\Domain\Entity\BoletoRemessaItem;
use App\Cobranca\Domain\Event\BoletoRemessaGerada;
use App\Cobranca\Domain\Repository\BoletoRemessaItemRepositoryInterface;
use App\Cobranca\Domain\Repository\BoletoRemessaRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Financeiro\Domain\Entity\TituloParcela;
use App\Financeiro\Domain\Repository\TituloParcelaRepositoryInterface;
use App\Shared\Application\Bus\EventBusInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class BoletoRemessaService
{
    public function __construct(private readonly BoletoRemessaRepositoryInterface $repo, private readonly BoletoRemessaItemRepositoryInterface $itemRepo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly ContaFinanceiraRepositoryInterface $contaRepo, private readonly TituloParcelaRepositoryInterface $parcelaRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit, private readonly EventBusInterface $eventBus) {}
    /** @return array{remessa:BoletoRemessa,itens:list<BoletoRemessaItem>} */
    public function create(CreateBoletoRemessaRequest $r): array
    {
        $this->validator->validate($r);
        $company = $this->companyRepo->findById((int) $r->companyId); $empresa = $this->empresaRepo->findById((int) $r->empresaId); $unidade = $this->unidadeRepo->findById((int) $r->unidadeId); $conta = $this->contaRepo->findById((int) $r->contaFinanceiraId);
        if (!$company || !$empresa || !$unidade || !$conta) { throw new ResourceNotFoundException('Contexto de cobrança inválido.'); }
        if ($empresa->getCompanyId() !== $company->getId() || $unidade->getCompanyId() !== $company->getId() || $conta->getCompanyId() !== $company->getId() || $conta->getEmpresa()->getId() !== $empresa->getId()) { throw new ValidationException(['contexto' => ['Company, empresa, unidade e conta financeira devem ser compatíveis.']]); }
        $parcelas = array_map(fn (int $id): TituloParcela => $this->resolveParcela($id, (int) $company->getId(), (int) $empresa->getId(), (int) $unidade->getId()), $r->parcelaIds);
        return $this->tx->run(function () use ($company, $empresa, $unidade, $conta, $parcelas, $r): array {
            $codigoRemessa = sprintf('BRM-%d-%s', (int) $company->getId(), date('YmdHis'));
            $remessa = new BoletoRemessa($company, $empresa, $unidade, $conta, $codigoRemessa, $r->banco);
            $this->repo->save($remessa);
            $itens = [];
            foreach ($parcelas as $parcela) {
                $item = new BoletoRemessaItem($remessa, $parcela, $this->gerarNossoNumero((int) $company->getId(), $parcela), $parcela->getValorAberto(), $parcela->getVencimento());
                $this->itemRepo->save($item);
                $itens[] = $item;
            }
            $this->audit->log((int) $company->getId(), 'boleto_remessa', 'cobranca.boleto.remessa.gerada', ['remessaId' => $remessa->getId(), 'itens' => count($itens)]);
            $this->eventBus->publish(new BoletoRemessaGerada((int) $remessa->getId(), (int) $company->getId()));
            return ['remessa' => $remessa, 'itens' => $itens];
        });
    }
    private function resolveParcela(int $id, int $companyId, int $empresaId, int $unidadeId): TituloParcela
    {
        $parcela = $this->parcelaRepo->findById($id);
        if ($parcela === null) { throw new ResourceNotFoundException(sprintf('Parcela %d não encontrada.', $id)); }
        if ($parcela->getCompanyId() !== $companyId || $parcela->getEmpresa()->getId() !== $empresaId || $parcela->getUnidade()->getId() !== $unidadeId) { throw new ValidationException(['parcelaIds' => [sprintf('Parcela %d fora do contexto informado.', $id)]]); }
        if ($parcela->getTitulo()->getTipo() !== 'receber') { throw new ValidationException(['parcelaIds' => [sprintf('Parcela %d não pertence a um título a receber.', $id)]]); }
        if ((float) $parcela->getValorAberto() <= 0.0) { throw new ValidationException(['parcelaIds' => [sprintf('Parcela %d não possui saldo aberto.', $id)]]); }
        return $parcela;
    }
    private function gerarNossoNumero(int $companyId, TituloParcela $parcela): string { return sprintf('%d%06d%s', $companyId, (int) $parcela->getId(), substr(md5((string) microtime(true) . '-' . $parcela->getId()), 0, 6)); }
}
