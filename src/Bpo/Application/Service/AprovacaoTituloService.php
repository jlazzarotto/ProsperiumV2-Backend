<?php
declare(strict_types=1);
namespace App\Bpo\Application\Service;
use App\Bpo\Application\DTO\CreateAprovacaoTituloRequest;
use App\Bpo\Domain\Entity\AprovacaoTitulo;
use App\Bpo\Domain\Entity\AprovacaoTituloItem;
use App\Bpo\Domain\Entity\NotificacaoSistema;
use App\Bpo\Domain\Event\AprovacaoTituloSolicitada;
use App\Bpo\Domain\Repository\AprovacaoTituloItemRepositoryInterface;
use App\Bpo\Domain\Repository\AprovacaoTituloRepositoryInterface;
use App\Bpo\Domain\Repository\NotificacaoSistemaRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Financeiro\Domain\Repository\TituloRepositoryInterface;
use App\Identity\Domain\Repository\UserAlcadaRepositoryInterface;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Identity\Domain\Repository\UserEmpresaRepositoryInterface;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Identity\Domain\Repository\UserUnidadeRepositoryInterface;
use App\Shared\Application\Bus\EventBusInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\AuthenticatedUserProviderInterface;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class AprovacaoTituloService
{
    public function __construct(private readonly AprovacaoTituloRepositoryInterface $repo, private readonly AprovacaoTituloItemRepositoryInterface $itemRepo, private readonly NotificacaoSistemaRepositoryInterface $notificacaoRepo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly TituloRepositoryInterface $tituloRepo, private readonly UserRepositoryInterface $userRepo, private readonly UserAlcadaRepositoryInterface $alcadaRepo, private readonly UserCompanyRepositoryInterface $userCompanyRepo, private readonly UserEmpresaRepositoryInterface $userEmpresaRepo, private readonly UserUnidadeRepositoryInterface $userUnidadeRepo, private readonly AutomacaoNotificacaoService $automacaoService, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit, private readonly EventBusInterface $eventBus, private readonly AuthenticatedUserProviderInterface $authenticatedUserProvider) {}
    /** @return array{aprovacao:AprovacaoTitulo,itens:list<AprovacaoTituloItem>} */
    public function create(CreateAprovacaoTituloRequest $r): array
    {
        $this->validator->validate($r);
        $authenticatedUser = $this->authenticatedUserProvider->requireUser();
        $company = $this->companyRepo->findById((int) $r->companyId); $empresa = $this->empresaRepo->findById((int) $r->empresaId); $unidade = $this->unidadeRepo->findById((int) $r->unidadeId); $titulo = $this->tituloRepo->findById((int) $r->tituloId); $solicitante = $this->userRepo->findById($authenticatedUser->id);
        if (!$company || !$empresa || !$unidade || !$titulo || !$solicitante) { throw new ResourceNotFoundException('Contexto de aprovação inválido.'); }
        if ($titulo->getCompany()->getId() !== $company->getId() || $titulo->getEmpresa()->getId() !== $empresa->getId() || $titulo->getUnidade()->getId() !== $unidade->getId()) { throw new ValidationException(['tituloId' => ['Título fora do contexto informado.']]); }
        $tipoOperacao = $r->tipoOperacao ?? sprintf('aprovacao_titulo_%s', $titulo->getTipo());
        $valor = $titulo->getValorTotal();
        $aprovadores = [];
        foreach ($r->aprovadorIds as $aprovadorId) {
            $aprovador = $this->userRepo->findById((int) $aprovadorId);
            if ($aprovador === null) { throw new ResourceNotFoundException(sprintf('Aprovador %d não encontrado.', $aprovadorId)); }
            if (!$this->usuarioPossuiEscopo((int) $aprovador->getId(), (int) $company->getId(), (int) $empresa->getId(), (int) $unidade->getId())) { throw new ValidationException(['aprovadorIds' => [sprintf('Usuário %d não possui escopo ativo para a company, empresa e unidade informadas.', $aprovadorId)]]); }
            if (!$this->alcadaRepo->userHasActiveAlcadaForValue((int) $aprovador->getId(), (int) $company->getId(), (int) $empresa->getId(), (int) $unidade->getId(), $tipoOperacao, $valor)) { throw new ValidationException(['aprovadorIds' => [sprintf('Usuário %d não possui alçada para %s no valor %s.', $aprovadorId, $tipoOperacao, $valor)]]); }
            $aprovadores[] = $aprovador;
        }
        return $this->tx->run(function () use ($company, $empresa, $unidade, $titulo, $solicitante, $tipoOperacao, $valor, $aprovadores): array {
            $aprovacao = new AprovacaoTitulo($company, $empresa, $unidade, $titulo, $solicitante, $tipoOperacao, $valor);
            $this->repo->save($aprovacao);
            $itens = [];
            foreach ($aprovadores as $ordem => $aprovador) {
                $item = new AprovacaoTituloItem($aprovacao, $aprovador, $ordem + 1, $valor);
                $this->itemRepo->save($item);
                $this->notificacaoRepo->save(new NotificacaoSistema($company, $empresa, $unidade, $aprovador, 'aprovacao_titulo', 'Nova aprovação pendente', sprintf('O título %d foi enviado para sua aprovação.', $titulo->getId()), ['aprovacaoId' => $aprovacao->getId(), 'tituloId' => $titulo->getId()]));
                $itens[] = $item;
            }
            $this->automacaoService->aplicarParaTitulo($titulo, 'aprovacao', $solicitante);
            $this->audit->log((int) $company->getId(), 'aprovacao_titulo', 'bpo.aprovacao_titulo.criada', ['aprovacaoId' => $aprovacao->getId(), 'tituloId' => $titulo->getId()]);
            $this->eventBus->publish(new AprovacaoTituloSolicitada((int) $aprovacao->getId(), (int) $company->getId()));
            return ['aprovacao' => $aprovacao, 'itens' => $itens];
        });
    }

    private function usuarioPossuiEscopo(int $userId, int $companyId, int $empresaId, int $unidadeId): bool
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
