<?php
declare(strict_types=1);
namespace App\Cobranca\Application\Service;
use App\Cobranca\Application\DTO\ImportarBoletoRetornoRequest;
use App\Cobranca\Domain\Entity\BoletoRetornoItem;
use App\Cobranca\Domain\Event\BoletoRetornoImportado;
use App\Cobranca\Domain\Repository\BoletoRemessaItemRepositoryInterface;
use App\Cobranca\Domain\Repository\BoletoRemessaRepositoryInterface;
use App\Cobranca\Domain\Repository\BoletoRetornoItemRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Bus\EventBusInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class BoletoRetornoService
{
    public function __construct(private readonly BoletoRetornoItemRepositoryInterface $repo, private readonly BoletoRemessaRepositoryInterface $remessaRepo, private readonly BoletoRemessaItemRepositoryInterface $remessaItemRepo, private readonly CompanyRepositoryInterface $companyRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit, private readonly EventBusInterface $eventBus) {}
    /** @return list<BoletoRetornoItem> */
    public function import(ImportarBoletoRetornoRequest $r): array
    {
        $this->validator->validate($r);
        $company = $this->companyRepo->findById((int) $r->companyId); if ($company === null) { throw new ResourceNotFoundException('Company não encontrada.'); }
        $remessa = $r->remessaId !== null ? $this->remessaRepo->findById($r->remessaId) : null;
        if ($r->remessaId !== null && ($remessa === null || $remessa->getCompanyId() !== $company->getId())) { throw new ValidationException(['remessaId' => ['Remessa inválida para a company informada.']]); }
        return $this->tx->run(function () use ($r, $company, $remessa): array {
            $importados = [];
            foreach ($r->itens as $payload) {
                $itemRemessa = $this->remessaItemRepo->findByNossoNumero((string) $payload['nossoNumero']);
                if ($itemRemessa === null) { throw new ValidationException(['itens' => [sprintf('Nosso número %s não encontrado.', $payload['nossoNumero'])]]); }
                if ($itemRemessa->getRemessa()->getCompanyId() !== $company->getId()) { throw new ValidationException(['itens' => [sprintf('Nosso número %s não pertence à company informada.', $payload['nossoNumero'])]]); }
                if ($remessa !== null && $itemRemessa->getRemessa()->getId() !== $remessa->getId()) { throw new ValidationException(['itens' => [sprintf('Nosso número %s não pertence à remessa informada.', $payload['nossoNumero'])]]); }
                $status = $this->mapearStatusOcorrencia((string) $payload['codigoOcorrencia']);
                $dataOcorrencia = new \DateTimeImmutable((string) ($payload['dataOcorrencia'] ?? 'now'));
                $itemRemessa->registrarRetorno((string) $payload['codigoOcorrencia'], $status, $dataOcorrencia);
                $retorno = new BoletoRetornoItem($company, $itemRemessa->getRemessa()->getEmpresa(), $itemRemessa->getRemessa()->getUnidade(), $itemRemessa, (string) $payload['nossoNumero'], (string) $payload['codigoOcorrencia'], $payload['descricao'] ?? null, number_format((float) ($payload['valorRecebido'] ?? '0'), 2, '.', ''), $dataOcorrencia, $payload['linhaOriginal'] ?? null);
                $this->repo->save($retorno);
                $this->remessaItemRepo->save($itemRemessa);
                $importados[] = $retorno;
            }
            $this->audit->log((int) $company->getId(), 'boleto_retorno', 'cobranca.boleto.retorno.importado', ['itens' => count($importados)]);
            $this->eventBus->publish(new BoletoRetornoImportado((int) $company->getId(), count($importados)));
            return $importados;
        });
    }
    private function mapearStatusOcorrencia(string $codigo): string
    {
        return match (strtoupper(trim($codigo))) {
            '06', 'LIQUIDADO', 'RECEBIDO' => 'liquidado',
            '02', 'ENTRADA_CONFIRMADA', 'REGISTRADO' => 'registrado',
            default => 'retornado',
        };
    }
}
