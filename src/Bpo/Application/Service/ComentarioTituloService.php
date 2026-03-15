<?php
declare(strict_types=1);
namespace App\Bpo\Application\Service;
use App\Bpo\Application\DTO\CreateComentarioTituloRequest;
use App\Bpo\Domain\Entity\ComentarioTitulo;
use App\Bpo\Domain\Event\ComentarioTituloRegistrado;
use App\Bpo\Domain\Repository\ComentarioTituloRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Financeiro\Domain\Repository\TituloRepositoryInterface;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Shared\Application\Bus\EventBusInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\AuthenticatedUserProviderInterface;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class ComentarioTituloService
{
    public function __construct(private readonly ComentarioTituloRepositoryInterface $repo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly TituloRepositoryInterface $tituloRepo, private readonly UserRepositoryInterface $userRepo, private readonly AutomacaoNotificacaoService $automacaoService, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit, private readonly EventBusInterface $eventBus, private readonly AuthenticatedUserProviderInterface $authenticatedUserProvider) {}
    public function create(int $tituloId, CreateComentarioTituloRequest $r): ComentarioTitulo
    {
        $this->validator->validate($r);
        $authenticatedUser = $this->authenticatedUserProvider->requireUser();
        $company = $this->companyRepo->findById((int) $r->companyId); $empresa = $this->empresaRepo->findById((int) $r->empresaId); $unidade = $this->unidadeRepo->findById((int) $r->unidadeId); $titulo = $this->tituloRepo->findById($tituloId); $user = $this->userRepo->findById($authenticatedUser->id);
        if (!$company || !$empresa || !$unidade || !$titulo || !$user) { throw new ResourceNotFoundException('Contexto de comentário inválido.'); }
        if ($titulo->getCompany()->getId() !== $company->getId() || $titulo->getEmpresa()->getId() !== $empresa->getId() || $titulo->getUnidade()->getId() !== $unidade->getId()) { throw new ValidationException(['tituloId' => ['Título fora do contexto informado.']]); }
        return $this->tx->run(function () use ($company, $empresa, $unidade, $titulo, $user, $r): ComentarioTitulo {
            $comentario = new ComentarioTitulo($company, $empresa, $unidade, $titulo, $user, $r->comentario);
            $this->repo->save($comentario);
            $this->automacaoService->aplicarParaTitulo($titulo, 'comentario', $user);
            $this->audit->log((int) $company->getId(), 'titulo_comentario', 'bpo.titulo.comentario.criado', ['tituloId' => $titulo->getId(), 'comentarioId' => $comentario->getId()]);
            $this->eventBus->publish(new ComentarioTituloRegistrado((int) $titulo->getId(), (int) $comentario->getId(), (int) $company->getId()));
            return $comentario;
        });
    }
}
