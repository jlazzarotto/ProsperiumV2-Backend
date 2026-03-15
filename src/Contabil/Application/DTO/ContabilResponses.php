<?php
declare(strict_types=1);
namespace App\Contabil\Application\DTO;
use App\Contabil\Domain\Entity\ContaContabil;
use App\Contabil\Domain\Entity\IndicadorFinanceiro;
use App\Contabil\Domain\Entity\LancamentoContabil;
use App\Contabil\Domain\Entity\LancamentoContabilItem;
use App\Contabil\Domain\Entity\SnapshotFluxoCaixa;
final class ContabilResponses
{
    public static function conta(ContaContabil $conta): array { return ['id' => $conta->getId(), 'companyId' => $conta->getCompany()->getId(), 'parentId' => $conta->getParent()?->getId(), 'codigo' => $conta->getCodigo(), 'nome' => $conta->getNome(), 'tipo' => $conta->getTipo(), 'status' => $conta->getStatus()]; }
    public static function lancamento(LancamentoContabil $lancamento, array $itens): array { return ['id' => $lancamento->getId(), 'companyId' => $lancamento->getCompany()->getId(), 'empresaId' => $lancamento->getEmpresa()->getId(), 'unidadeId' => $lancamento->getUnidade()->getId(), 'tituloId' => $lancamento->getTitulo()?->getId(), 'dataLancamento' => $lancamento->getDataLancamento()->format('Y-m-d'), 'historico' => $lancamento->getHistorico(), 'status' => $lancamento->getStatus(), 'itens' => array_map([self::class, 'lancamentoItem'], $itens)]; }
    public static function lancamentoItem(LancamentoContabilItem $item): array { return ['id' => $item->getId(), 'contaContabilId' => $item->getContaContabil()->getId(), 'natureza' => $item->getNatureza(), 'valor' => $item->getValor()]; }
    public static function indicador(IndicadorFinanceiro $indicador): array { return ['id' => $indicador->getId(), 'codigo' => $indicador->getCodigo(), 'nome' => $indicador->getNome(), 'dataReferencia' => $indicador->getDataReferencia()->format('Y-m-d'), 'valor' => $indicador->getValor(), 'metadata' => $indicador->getMetadataJson()]; }
    public static function snapshot(SnapshotFluxoCaixa $snapshot): array { return ['dataReferencia' => $snapshot->getDataReferencia()->format('Y-m-d'), 'saldoInicial' => $snapshot->getSaldoInicial(), 'entradasPeriodo' => $snapshot->getEntradasPeriodo(), 'saidasPeriodo' => $snapshot->getSaidasPeriodo(), 'saldoFinal' => $snapshot->getSaldoFinal()]; }
}
