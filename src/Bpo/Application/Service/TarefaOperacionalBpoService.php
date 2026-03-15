<?php
declare(strict_types=1);
namespace App\Bpo\Application\Service;
use App\Bpo\Application\DTO\CreateTarefaBpoRequest;
use App\Bpo\Domain\Entity\NotificacaoSistema;
use App\Bpo\Domain\Entity\TarefaOperacionalBpo;
use App\Bpo\Domain\Entity\TarefaOperacionalHistorico;
use App\Bpo\Domain\Event\TarefaBpoCriada;
use App\Bpo\Domain\Repository\NotificacaoSistemaRepositoryInterface;
use App\Bpo\Domain\Repository\TarefaOperacionalBpoRepositoryInterface;
use App\Bpo\Domain\Repository\TarefaOperacionalHistoricoRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Financeiro\Domain\Repository\TituloRepositoryInterface;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Identity\Domain\Repository\UserEmpresaRepositoryInterface;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Identity\Domain\Repository\UserUnidadeRepositoryInterface;
use App\Shared\Application\Bus\EventBusInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class TarefaOperacionalBpoService
{
    public function __construct(private readonly TarefaOperacionalBpoRepositoryInterface $repo, private readonly TarefaOperacionalHistoricoRepositoryInterface $historicoRepo, private readonly NotificacaoSistemaRepositoryInterface $notificacaoRepo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly TituloRepositoryInterface $tituloRepo, private readonly UserRepositoryInterface $userRepo, private readonly UserCompanyRepositoryInterface $userCompanyRepo, private readonly UserEmpresaRepositoryInterface $userEmpresaRepo, private readonly UserUnidadeRepositoryInterface $userUnidadeRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit, private readonly EventBusInterface $eventBus) {}
    public function create(CreateTarefaBpoRequest $r): TarefaOperacionalBpo
    {
        $this->validator->validate($r);
        $company = $this->companyRepo->findById((int) $r->companyId); $empresa = $this->empresaRepo->findById((int) $r->empresaId); $unidade = $this->unidadeRepo->findById((int) $r->unidadeId);
        if (!$company || !$empresa || !$unidade) { throw new ResourceNotFoundException('Contexto BPO inválido.'); }
        if ($empresa->getCompany()->getId() !== $company->getId() || $unidade->getCompany()->getId() !== $company->getId()) { throw new ValidationException(['contexto' => ['Company, empresa e unidade devem ser compatíveis.']]); }
        $titulo = $r->tituloId !== null ? $this->tituloRepo->findById($r->tituloId) : null; $responsavel = $r->responsavelUserId !== null ? $this->userRepo->findById($r->responsavelUserId) : null;
        if ($titulo !== null && ($titulo->getCompany()->getId() !== $company->getId() || $titulo->getEmpresa()->getId() !== $empresa->getId() || $titulo->getUnidade()->getId() !== $unidade->getId())) { throw new ValidationException(['tituloId' => ['Título fora do contexto informado.']]); }
        if ($r->responsavelUserId !== null && $responsavel === null) { throw new ResourceNotFoundException('Usuário responsável não encontrado.'); }
        if ($responsavel !== null && !$this->responsavelPossuiEscopo((int) $responsavel->getId(), (int) $company->getId(), (int) $empresa->getId(), (int) $unidade->getId())) { throw new ValidationException(['responsavelUserId' => ['Usuário responsável não possui escopo ativo para a company, empresa e unidade informadas.']]); }
        return $this->tx->run(function () use ($company, $empresa, $unidade, $titulo, $responsavel, $r): TarefaOperacionalBpo {
            $tarefa = new TarefaOperacionalBpo($company, $empresa, $unidade, $titulo, $responsavel, $r->tipo, $r->descricao, $r->prioridade, $r->prazoEm !== null ? new \DateTimeImmutable($r->prazoEm) : null);
            $this->repo->save($tarefa);
            $this->historicoRepo->save(new TarefaOperacionalHistorico($tarefa, $responsavel, 'criada', 'Tarefa criada no fluxo BPO.'));
            if ($responsavel !== null) { $this->notificacaoRepo->save(new NotificacaoSistema($company, $empresa, $unidade, $responsavel, 'tarefa_bpo', 'Nova tarefa operacional', sprintf('Tarefa "%s" atribuída ao usuário %s.', $tarefa->getTipo(), $responsavel->getNome()), ['tarefaId' => $tarefa->getId()])); }
            $this->audit->log((int) $company->getId(), 'tarefa_bpo', 'bpo.tarefa.criada', ['tarefaId' => $tarefa->getId()]);
            $this->eventBus->publish(new TarefaBpoCriada((int) $tarefa->getId(), (int) $company->getId()));
            return $tarefa;
        });
    }
    /** @return list<TarefaOperacionalBpo> */ public function list(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $status = null): array { return $this->repo->listAll($companyId, $empresaId, $unidadeId, $status); }

    private function responsavelPossuiEscopo(int $userId, int $companyId, int $empresaId, int $unidadeId): bool
    {
        if (!$this->userCompanyRepo->userHasCompany($userId, $companyId)) {
            return false;
        }

        if ($this->userCompanyRepo->isCompanyAdmin($userId, $companyId)) {
            return true;
        }

        return $this->userEmpresaRepo->userHasEmpresa($userId, $companyId, $empresaId)
            && $this->userUnidadeRepo->userHasUnidade($userId, $companyId, $unidadeId);
    }
}
