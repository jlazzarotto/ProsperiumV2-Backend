<?php
declare(strict_types=1);
namespace App\Cobranca\Application\Service;
use App\Cobranca\Application\DTO\RegisterPixWebhookRequest;
use App\Cobranca\Domain\Entity\PixEventoWebhook;
use App\Cobranca\Domain\Entity\PixRecebimento;
use App\Cobranca\Domain\Event\PixWebhookRecebido;
use App\Cobranca\Domain\Repository\PixCobrancaRepositoryInterface;
use App\Cobranca\Domain\Repository\PixEventoWebhookRepositoryInterface;
use App\Cobranca\Domain\Repository\PixRecebimentoRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Shared\Application\Bus\EventBusInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class PixWebhookService
{
    public function __construct(private readonly PixEventoWebhookRepositoryInterface $eventoRepo, private readonly PixCobrancaRepositoryInterface $pixRepo, private readonly PixRecebimentoRepositoryInterface $recebimentoRepo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit, private readonly EventBusInterface $eventBus) {}
    public function register(RegisterPixWebhookRequest $r): PixEventoWebhook
    {
        $this->validator->validate($r);
        $company = $this->companyRepo->findById((int) $r->companyId); if ($company === null) { throw new ResourceNotFoundException('Company não encontrada.'); }
        $empresa = $r->empresaId !== null ? $this->empresaRepo->findById($r->empresaId) : null; $unidade = $r->unidadeId !== null ? $this->unidadeRepo->findById($r->unidadeId) : null;
        if (($empresa !== null && $empresa->getCompanyId() !== $company->getId()) || ($unidade !== null && $unidade->getCompanyId() !== $company->getId())) { throw new ValidationException(['contexto' => ['Empresa/unidade do webhook devem pertencer à mesma company.']]); }
        return $this->tx->run(function () use ($r, $company, $empresa, $unidade): PixEventoWebhook {
            $evento = new PixEventoWebhook($company, $empresa, $unidade, $r->tipoEvento, $r->endToEndId ?? $r->txid, $r->payload, $r->recebidoEm !== null ? new \DateTimeImmutable($r->recebidoEm) : null);
            $this->eventoRepo->save($evento);
            $pix = $r->pixCobrancaId !== null ? $this->pixRepo->findById($r->pixCobrancaId) : ($r->txid !== null ? $this->pixRepo->findByTxid($r->txid) : null);
            if ($pix !== null) {
                if ($pix->getCompanyId() !== $company->getId()) { throw new ValidationException(['pixCobrancaId' => ['Cobrança PIX não pertence à company informada.']]); }
                if (in_array(strtolower($r->tipoEvento), ['pix.recebido', 'pix_received', 'recebido'], true)) {
                    $endToEndId = trim((string) ($r->endToEndId ?? ('E2E-' . $pix->getTxid())));
                    if ($this->recebimentoRepo->findByEndToEndId($endToEndId) === null) {
                        $recebimento = new PixRecebimento($company, $pix->getEmpresa(), $pix->getUnidade(), $pix, $endToEndId, $pix->getTxid(), number_format((float) ($r->valor ?? $pix->getValor()), 2, '.', ''), $r->payload, $r->recebidoEm !== null ? new \DateTimeImmutable($r->recebidoEm) : new \DateTimeImmutable());
                        $this->recebimentoRepo->save($recebimento);
                    }
                    $pix->marcarRecebida();
                    $this->pixRepo->save($pix);
                }
            }
            $this->audit->log((int) $company->getId(), 'pix_webhook', 'cobranca.pix.webhook.recebido', ['eventoId' => $evento->getId(), 'tipoEvento' => $r->tipoEvento]);
            $this->eventBus->publish(new PixWebhookRecebido((int) $company->getId(), $r->tipoEvento));
            return $evento;
        });
    }
}
