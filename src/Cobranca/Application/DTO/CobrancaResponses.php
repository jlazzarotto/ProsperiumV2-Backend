<?php
declare(strict_types=1);
namespace App\Cobranca\Application\DTO;
use App\Cobranca\Domain\Entity\BoletoRemessa;
use App\Cobranca\Domain\Entity\BoletoRemessaItem;
use App\Cobranca\Domain\Entity\BoletoRetornoItem;
use App\Cobranca\Domain\Entity\PixCobranca;
use App\Cobranca\Domain\Entity\PixEventoWebhook;
final class CobrancaResponses
{
    public static function remessa(BoletoRemessa $remessa, array $itens): array { return ['id' => $remessa->getId(), 'companyId' => $remessa->getCompanyId(), 'empresaId' => $remessa->getEmpresa()->getId(), 'unidadeId' => $remessa->getUnidade()->getId(), 'contaFinanceiraId' => $remessa->getContaFinanceira()->getId(), 'codigoRemessa' => $remessa->getCodigoRemessa(), 'banco' => $remessa->getBanco(), 'status' => $remessa->getStatus(), 'itens' => array_map([self::class, 'remessaItem'], $itens)]; }
    public static function remessaItem(BoletoRemessaItem $item): array { return ['id' => $item->getId(), 'tituloParcelaId' => $item->getParcela()->getId(), 'nossoNumero' => $item->getNossoNumero(), 'valor' => $item->getValor(), 'vencimento' => $item->getVencimento()->format('Y-m-d'), 'status' => $item->getStatus(), 'ocorrenciaRetorno' => $item->getOcorrenciaRetorno()]; }
    public static function retornoItem(BoletoRetornoItem $item): array { return ['id' => $item->getId(), 'nossoNumero' => $item->getNossoNumero(), 'codigoOcorrencia' => $item->getCodigoOcorrencia(), 'valorRecebido' => $item->getValorRecebido(), 'dataOcorrencia' => $item->getDataOcorrencia()->format('Y-m-d')]; }
    public static function pixCobranca(PixCobranca $pix): array { return ['id' => $pix->getId(), 'companyId' => $pix->getCompanyId(), 'empresaId' => $pix->getEmpresa()->getId(), 'unidadeId' => $pix->getUnidade()->getId(), 'tituloParcelaId' => $pix->getParcela()->getId(), 'contaFinanceiraId' => $pix->getContaFinanceira()->getId(), 'txid' => $pix->getTxid(), 'chavePix' => $pix->getChavePix(), 'valor' => $pix->getValor(), 'status' => $pix->getStatus(), 'expiracaoSegundos' => $pix->getExpiracaoSegundos(), 'calendarioExpiraEm' => $pix->getCalendarioExpiraEm()->format(\DateTimeInterface::ATOM), 'qrCode' => $pix->getQrCode(), 'copiaCola' => $pix->getCopiaCola()]; }
    public static function webhook(PixEventoWebhook $evento): array { return ['id' => $evento->getId()]; }
}
