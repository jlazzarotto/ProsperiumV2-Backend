<?php
declare(strict_types=1);
namespace App\Financeiro\Application\DTO;
use App\Financeiro\Domain\Entity\AnexoFinanceiro;
use App\Financeiro\Domain\Entity\Baixa;
use App\Financeiro\Domain\Entity\Titulo;
use App\Financeiro\Domain\Entity\TituloParcela;
final class FinanceiroResponses
{
    /** @param list<TituloParcela> $parcelas @param list<AnexoFinanceiro> $anexos */
    public static function titulo(Titulo $t, array $parcelas = [], array $anexos = []): array { return ['id'=>$t->getId(),'companyId'=>$t->getCompanyId(),'empresaId'=>$t->getEmpresa()->getId(),'unidadeId'=>$t->getUnidade()->getId(),'pessoaId'=>$t->getPessoa()->getId(),'contaFinanceiraId'=>$t->getContaFinanceira()?->getId(),'tipo'=>$t->getTipo(),'numeroDocumento'=>$t->getNumeroDocumento(),'valorTotal'=>$t->getValorTotal(),'status'=>$t->getStatus(),'dataEmissao'=>$t->getDataEmissao()->format('Y-m-d'),'observacoes'=>$t->getObservacoes(),'parcelas'=>array_map([self::class,'parcela'],$parcelas),'anexos'=>array_map([self::class,'anexo'],$anexos)]; }
    public static function parcela(TituloParcela $p): array { return ['id'=>$p->getId(),'tituloId'=>$p->getTitulo()->getId(),'numero'=>$p->getNumero(),'valor'=>$p->getValor(),'valorAberto'=>$p->getValorAberto(),'vencimento'=>$p->getVencimento()->format('Y-m-d'),'status'=>$p->getStatus()]; }
    public static function baixa(Baixa $b): array { return ['id'=>$b->getId(),'parcelaId'=>$b->getParcela()->getId(),'contaFinanceiraId'=>$b->getContaFinanceira()->getId(),'valor'=>$b->getValor(),'dataPagamento'=>$b->getDataPagamento()->format('Y-m-d')]; }
    public static function anexo(AnexoFinanceiro $a): array { $r=new \ReflectionClass($a); $pid=$r->getProperty('id'); $pid->setAccessible(true); return ['id'=>$pid->getValue($a)]; }
}
