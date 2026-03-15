<?php
declare(strict_types=1);
namespace App\Tesouraria\Application\DTO;
use App\Tesouraria\Domain\Entity\ConciliacaoBancaria;
use App\Tesouraria\Domain\Entity\ConciliacaoRegra;
use App\Tesouraria\Domain\Entity\ExtratoBancario;
final class TesourariaResponses
{
    public static function extrato(ExtratoBancario $e, ?array $sugestao = null): array { return ['id'=>$e->getId(),'companyId'=>$e->getCompany()->getId(),'empresaId'=>$e->getEmpresa()->getId(),'unidadeId'=>$e->getUnidade()->getId(),'contaFinanceiraId'=>$e->getContaFinanceira()->getId(),'codigoExterno'=>$e->getCodigoExterno(),'dataMovimento'=>$e->getDataMovimento()->format('Y-m-d'),'valor'=>$e->getValor(),'tipo'=>$e->getTipo(),'descricao'=>$e->getDescricao(),'status'=>$e->getStatus(),'movimentoFinanceiroId'=>$e->getMovimentoFinanceiro()?->getId(),'baixaId'=>$e->getBaixa()?->getId(),'sugestao'=>$sugestao]; }
    public static function conciliacao(ConciliacaoBancaria $c): array { return ['id'=>$c->getId(),'extratoBancarioId'=>$c->getExtratoBancario()->getId(),'movimentoFinanceiroId'=>$c->getMovimentoFinanceiro()?->getId(),'baixaId'=>$c->getBaixa()?->getId(),'modo'=>$c->getModo()]; }
    public static function regra(ConciliacaoRegra $r): array { return ['id'=>$r->getId(),'companyId'=>$r->getCompany()->getId(),'empresaId'=>$r->getEmpresa()?->getId(),'unidadeId'=>$r->getUnidade()?->getId(),'contaFinanceiraId'=>$r->getContaFinanceira()?->getId(),'descricaoContains'=>$r->getDescricaoContains(),'tipoMovimentoSugerido'=>$r->getTipoMovimentoSugerido(),'aplicacao'=>$r->getAplicacao(),'status'=>$r->getStatus()]; }
}
