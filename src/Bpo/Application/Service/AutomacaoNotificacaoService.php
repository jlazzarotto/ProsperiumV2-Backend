<?php
declare(strict_types=1);
namespace App\Bpo\Application\Service;
use App\Bpo\Domain\Entity\NotificacaoSistema;
use App\Bpo\Domain\Repository\NotificacaoSistemaRepositoryInterface;
use App\Bpo\Domain\Repository\RegraAutomaticaClassificacaoRepositoryInterface;
use App\Financeiro\Domain\Entity\Titulo;
use App\Identity\Domain\Entity\User;
final class AutomacaoNotificacaoService
{
    public function __construct(private readonly RegraAutomaticaClassificacaoRepositoryInterface $regraRepo, private readonly NotificacaoSistemaRepositoryInterface $notificacaoRepo) {}
    public function aplicarParaTitulo(Titulo $titulo, string $origem, ?User $user = null): void
    {
        $texto = trim(($titulo->getNumeroDocumento() ?? '') . ' ' . ($titulo->getObservacoes() ?? ''));
        if ($texto === '') { return; }
        $regras = $this->regraRepo->findActiveMatches((int) $titulo->getCompanyId(), (int) $titulo->getEmpresa()->getId(), (int) $titulo->getUnidade()->getId(), $texto);
        foreach ($regras as $regra) {
            if (!$regra->isAcaoNotificacao()) { continue; }
            $this->notificacaoRepo->save(new NotificacaoSistema($titulo->getCompanyId(), $titulo->getEmpresa(), $titulo->getUnidade(), $user?->getId() !== null ? (int) $user->getId() : null, 'automacao_classificacao', 'Regra automática acionada', sprintf('Regra "%s" acionada para o título %d via %s.', $regra->getDescricaoContains(), $titulo->getId(), $origem), ['tituloId' => $titulo->getId(), 'origem' => $origem, 'regraId' => $regra->getId()]));
        }
    }
}
