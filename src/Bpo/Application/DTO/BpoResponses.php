<?php
declare(strict_types=1);
namespace App\Bpo\Application\DTO;
use App\Bpo\Domain\Entity\AprovacaoTitulo;
use App\Bpo\Domain\Entity\AprovacaoTituloItem;
use App\Bpo\Domain\Entity\ComentarioTitulo;
use App\Bpo\Domain\Entity\RegraAutomaticaClassificacao;
use App\Bpo\Domain\Entity\TarefaOperacionalBpo;
final class BpoResponses
{
    public static function tarefa(TarefaOperacionalBpo $tarefa): array { return ['id' => $tarefa->getId(), 'companyId' => $tarefa->getCompanyId(), 'empresaId' => $tarefa->getEmpresa()->getId(), 'unidadeId' => $tarefa->getUnidade()->getId(), 'tituloId' => $tarefa->getTitulo()?->getId(), 'responsavelUserId' => $tarefa->getResponsavelUserId(), 'tipo' => $tarefa->getTipo(), 'descricao' => $tarefa->getDescricao(), 'prioridade' => $tarefa->getPrioridade(), 'status' => $tarefa->getStatus(), 'prazoEm' => $tarefa->getPrazoEm()?->format(\DateTimeInterface::ATOM)]; }
    public static function comentario(ComentarioTitulo $comentario): array { return ['id' => $comentario->getId(), 'tituloId' => $comentario->getTitulo()->getId(), 'userId' => $comentario->getUserId(), 'comentario' => $comentario->getComentario(), 'createdAt' => $comentario->getCreatedAt()->format(\DateTimeInterface::ATOM)]; }
    public static function aprovacao(AprovacaoTitulo $aprovacao, array $itens): array { return ['id' => $aprovacao->getId(), 'tituloId' => $aprovacao->getTitulo()->getId(), 'solicitanteUserId' => $aprovacao->getSolicitanteUserId(), 'tipoOperacao' => $aprovacao->getTipoOperacao(), 'valorTotal' => $aprovacao->getValorTotal(), 'status' => $aprovacao->getStatus(), 'itens' => array_map([self::class, 'aprovacaoItem'], $itens)]; }
    public static function aprovacaoItem(AprovacaoTituloItem $item): array { return ['id' => $item->getId(), 'aprovadorUserId' => $item->getAprovadorUserId(), 'status' => $item->getStatus()]; }
    public static function regra(RegraAutomaticaClassificacao $regra): array { return ['id' => $regra->getId(), 'companyId' => $regra->getCompanyId(), 'empresaId' => $regra->getEmpresa()?->getId(), 'unidadeId' => $regra->getUnidade()?->getId(), 'categoriaFinanceiraId' => $regra->getCategoriaFinanceira()?->getId(), 'centroCustoId' => $regra->getCentroCusto()?->getId(), 'descricaoContains' => $regra->getDescricaoContains(), 'acaoNotificacao' => $regra->isAcaoNotificacao(), 'status' => $regra->getStatus()]; }
}
