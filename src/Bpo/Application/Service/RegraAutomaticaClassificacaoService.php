<?php
declare(strict_types=1);
namespace App\Bpo\Application\Service;
use App\Bpo\Application\DTO\CreateRegraAutomaticaClassificacaoRequest;
use App\Bpo\Domain\Entity\RegraAutomaticaClassificacao;
use App\Bpo\Domain\Event\RegraAutomaticaClassificacaoCriada;
use App\Bpo\Domain\Repository\RegraAutomaticaClassificacaoRepositoryInterface;
use App\Cadastro\Domain\Repository\CategoriaFinanceiraRepositoryInterface;
use App\Cadastro\Domain\Repository\CentroCustoRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Shared\Application\Bus\EventBusInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class RegraAutomaticaClassificacaoService
{
    public function __construct(private readonly RegraAutomaticaClassificacaoRepositoryInterface $repo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly UnidadeNegocioRepositoryInterface $unidadeRepo, private readonly CategoriaFinanceiraRepositoryInterface $categoriaRepo, private readonly CentroCustoRepositoryInterface $centroRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit, private readonly EventBusInterface $eventBus) {}
    public function create(CreateRegraAutomaticaClassificacaoRequest $r): RegraAutomaticaClassificacao
    {
        $this->validator->validate($r);
        $company = $this->companyRepo->findById((int) $r->companyId); if ($company === null) { throw new ResourceNotFoundException('Company não encontrada.'); }
        $empresa = $r->empresaId !== null ? $this->empresaRepo->findById($r->empresaId) : null; $unidade = $r->unidadeId !== null ? $this->unidadeRepo->findById($r->unidadeId) : null; $categoria = $r->categoriaFinanceiraId !== null ? $this->categoriaRepo->findById($r->categoriaFinanceiraId) : null; $centro = $r->centroCustoId !== null ? $this->centroRepo->findById($r->centroCustoId) : null;
        if (($empresa !== null && $empresa->getCompany()->getId() !== $company->getId()) || ($unidade !== null && $unidade->getCompany()->getId() !== $company->getId()) || ($categoria !== null && $categoria->getCompany()->getId() !== $company->getId()) || ($centro !== null && $centro->getCompany()->getId() !== $company->getId())) { throw new ValidationException(['contexto' => ['Empresa, unidade, categoria e centro de custo devem pertencer à mesma company.']]); }
        return $this->tx->run(function () use ($company, $empresa, $unidade, $categoria, $centro, $r): RegraAutomaticaClassificacao {
            $regra = new RegraAutomaticaClassificacao($company, $empresa, $unidade, $categoria, $centro, $r->descricaoContains, $r->acaoNotificacao, $r->status);
            $this->repo->save($regra);
            $this->audit->log((int) $company->getId(), 'regra_automatica_classificacao', 'bpo.regra_automatica.criada', ['regraId' => $regra->getId()]);
            $this->eventBus->publish(new RegraAutomaticaClassificacaoCriada((int) $regra->getId(), (int) $company->getId()));
            return $regra;
        });
    }
}
