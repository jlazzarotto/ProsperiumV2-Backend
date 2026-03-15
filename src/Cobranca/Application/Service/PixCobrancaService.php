<?php
declare(strict_types=1);
namespace App\Cobranca\Application\Service;
use App\Cadastro\Domain\Repository\ContaFinanceiraRepositoryInterface;
use App\Cobranca\Application\DTO\CreatePixCobrancaRequest;
use App\Cobranca\Domain\Entity\PixCobranca;
use App\Cobranca\Domain\Event\PixCobrancaCriada;
use App\Cobranca\Domain\Repository\PixCobrancaRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Financeiro\Domain\Repository\TituloParcelaRepositoryInterface;
use App\Shared\Application\Bus\EventBusInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class PixCobrancaService
{
    public function __construct(private readonly PixCobrancaRepositoryInterface $repo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly TituloParcelaRepositoryInterface $parcelaRepo, private readonly ContaFinanceiraRepositoryInterface $contaRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit, private readonly EventBusInterface $eventBus) {}
    public function create(CreatePixCobrancaRequest $r): PixCobranca
    {
        $this->validator->validate($r);
        $company = $this->companyRepo->findById((int) $r->companyId); $empresa = $this->empresaRepo->findById((int) $r->empresaId); $unidade = $this->unidadeRepo->findById((int) $r->unidadeId); $parcela = $this->parcelaRepo->findById((int) $r->parcelaId); $conta = $this->contaRepo->findById((int) $r->contaFinanceiraId);
        if (!$company || !$empresa || !$unidade || !$parcela || !$conta) { throw new ResourceNotFoundException('Contexto PIX inválido.'); }
        if ($empresa->getCompany()->getId() !== $company->getId() || $unidade->getCompany()->getId() !== $company->getId() || $conta->getCompany()->getId() !== $company->getId() || $conta->getEmpresa()->getId() !== $empresa->getId()) { throw new ValidationException(['contexto' => ['Company, empresa, unidade e conta financeira devem ser compatíveis.']]); }
        if ($parcela->getCompany()->getId() !== $company->getId() || $parcela->getEmpresa()->getId() !== $empresa->getId() || $parcela->getUnidade()->getId() !== $unidade->getId()) { throw new ValidationException(['parcelaId' => ['Parcela fora do contexto informado.']]); }
        if ($parcela->getTitulo()->getTipo() !== 'receber') { throw new ValidationException(['parcelaId' => ['Somente títulos a receber podem gerar cobrança PIX.']]); }
        if ((float) $r->valor > (float) $parcela->getValorAberto()) { throw new ValidationException(['valor' => ['Valor da cobrança PIX não pode exceder o saldo aberto da parcela.']]); }
        return $this->tx->run(function () use ($company, $empresa, $unidade, $parcela, $conta, $r): PixCobranca {
            $txid = $r->txid !== null ? trim($r->txid) : sprintf('PIX-%d-%s', (int) $company->getId(), substr(md5((string) microtime(true)), 0, 18));
            if ($this->repo->findByTxid($txid) !== null) { throw new ValidationException(['txid' => ['Já existe cobrança PIX com o txid informado.']]); }
            $pix = new PixCobranca($company, $empresa, $unidade, $parcela, $conta, $txid, $r->chavePix, number_format((float) $r->valor, 2, '.', ''), $r->expiracaoSegundos, $r->qrCode ?? sprintf('qr:%s', $txid), $r->copiaCola ?? sprintf('pix://%s', $txid));
            $this->repo->save($pix);
            $this->audit->log((int) $company->getId(), 'pix_cobranca', 'cobranca.pix.cobranca.criada', ['pixCobrancaId' => $pix->getId(), 'txid' => $pix->getTxid()]);
            $this->eventBus->publish(new PixCobrancaCriada((int) $pix->getId(), (int) $company->getId()));
            return $pix;
        });
    }
    /** @return list<PixCobranca> */ public function list(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $status = null): array { return $this->repo->listAll($companyId, $empresaId, $unidadeId, $status); }
}
