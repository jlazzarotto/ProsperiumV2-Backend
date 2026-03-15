<?php
declare(strict_types=1);
namespace App\Cadastro\Application\DTO;
use App\Cadastro\Domain\Entity\CategoriaFinanceira;
use App\Cadastro\Domain\Entity\CentroCusto;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Cadastro\Domain\Entity\FormaPagamento;
use App\Cadastro\Domain\Entity\Banco;
use App\Cadastro\Domain\Entity\Municipio;
use App\Cadastro\Domain\Entity\Pais;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Cadastro\Domain\Entity\Uf;
final class CadastroResponses
{
    public static function banco(Banco $b): array { return ['id'=>$b->getId(),'codigoCompe'=>$b->getCodigoCompe(),'nome'=>$b->getNome(),'ispb'=>$b->getIspb(),'documento'=>$b->getDocumento(),'nomeCurto'=>$b->getNomeCurto(),'rede'=>$b->getRede(),'tipo'=>$b->getTipo(),'tipoPix'=>$b->getTipoPix(),'site'=>$b->getSite(),'dataInicioOperacao'=>$b->getDataInicioOperacao()?->format('Y-m-d'),'dataInicioPix'=>$b->getDataInicioPix()?->format(DATE_ATOM),'dataRegistroOrigem'=>$b->getDataRegistroOrigem()?->format(DATE_ATOM),'dataAtualizacaoOrigem'=>$b->getDataAtualizacaoOrigem()?->format(DATE_ATOM),'status'=>$b->getStatus()]; }
    public static function municipio(Municipio $m): array { return ['id'=>$m->getId(),'codigoIbge'=>$m->getCodigoIbge(),'nome'=>$m->getNome(),'ufCodigoIbge'=>$m->getUfCodigoIbge(),'ufSigla'=>$m->getUfSigla(),'ufNome'=>$m->getUfNome(),'regiaoCodigoIbge'=>$m->getRegiaoCodigoIbge(),'regiaoSigla'=>$m->getRegiaoSigla(),'regiaoNome'=>$m->getRegiaoNome(),'regiaoIntermediariaCodigoIbge'=>$m->getRegiaoIntermediariaCodigoIbge(),'regiaoIntermediariaNome'=>$m->getRegiaoIntermediariaNome(),'regiaoImediataCodigoIbge'=>$m->getRegiaoImediataCodigoIbge(),'regiaoImediataNome'=>$m->getRegiaoImediataNome(),'microrregiaoCodigoIbge'=>$m->getMicrorregiaoCodigoIbge(),'microrregiaoNome'=>$m->getMicrorregiaoNome(),'mesorregiaoCodigoIbge'=>$m->getMesorregiaoCodigoIbge(),'mesorregiaoNome'=>$m->getMesorregiaoNome(),'status'=>$m->getStatus()]; }
    public static function pais(Pais $p): array { return ['id'=>$p->getId(),'codigoM49'=>$p->getCodigoM49(),'isoAlpha2'=>$p->getIsoAlpha2(),'isoAlpha3'=>$p->getIsoAlpha3(),'nome'=>$p->getNome(),'regiaoCodigoM49'=>$p->getRegiaoCodigoM49(),'regiaoNome'=>$p->getRegiaoNome(),'subRegiaoCodigoM49'=>$p->getSubRegiaoCodigoM49(),'subRegiaoNome'=>$p->getSubRegiaoNome(),'regiaoIntermediariaCodigoM49'=>$p->getRegiaoIntermediariaCodigoM49(),'regiaoIntermediariaNome'=>$p->getRegiaoIntermediariaNome(),'status'=>$p->getStatus()]; }
    public static function uf(Uf $u): array { return ['id'=>$u->getId(),'codigoIbge'=>$u->getCodigoIbge(),'sigla'=>$u->getSigla(),'nome'=>$u->getNome(),'regiaoCodigoIbge'=>$u->getRegiaoCodigoIbge(),'regiaoSigla'=>$u->getRegiaoSigla(),'regiaoNome'=>$u->getRegiaoNome(),'status'=>$u->getStatus()]; }
    public static function pessoa(Pessoa $p): array { return ['id'=>$p->getId(),'companyId'=>$p->getCompany()->getId(),'empresaId'=>$p->getEmpresa()?->getId(),'nome'=>$p->getNome(),'documento'=>$p->getDocumento(),'classificacao'=>$p->getClassificacao(),'status'=>$p->getStatus()]; }
    public static function categoria(CategoriaFinanceira $c): array { return ['id'=>$c->getId(),'companyId'=>$c->getCompany()->getId(),'parentId'=>$c->getParent()?->getId(),'codigo'=>$c->getCodigo(),'nome'=>$c->getNome(),'tipo'=>$c->getTipo(),'status'=>$c->getStatus()]; }
    public static function centro(CentroCusto $c): array { return ['id'=>$c->getId(),'companyId'=>$c->getCompany()->getId(),'parentId'=>$c->getParent()?->getId(),'codigo'=>$c->getCodigo(),'nome'=>$c->getNome(),'status'=>$c->getStatus()]; }
    public static function conta(ContaFinanceira $c): array { return ['id'=>$c->getId(),'companyId'=>$c->getCompany()->getId(),'empresaId'=>$c->getEmpresa()->getId(),'unidadeId'=>$c->getUnidade()?->getId(),'bancoId'=>$c->getBanco()?->getId(),'banco'=>$c->getBanco()!==null?self::banco($c->getBanco()):null,'titularPessoaId'=>$c->getTitularPessoa()?->getId(),'titularPessoaNome'=>$c->getTitularPessoa()?->getNome(),'codigo'=>$c->getCodigo(),'nome'=>$c->getNome(),'tipo'=>$c->getTipo(),'agencia'=>$c->getAgencia(),'contaNumero'=>$c->getContaNumero(),'contaDigito'=>$c->getContaDigito(),'saldoInicial'=>$c->getSaldoInicial(),'dataSaldoInicial'=>$c->getDataSaldoInicial()?->format('Y-m-d'),'permiteMovimentoNegativo'=>$c->isPermiteMovimentoNegativo(),'status'=>$c->getStatus()]; }
    public static function forma(FormaPagamento $f): array { return ['id'=>$f->getId(),'companyId'=>$f->getCompany()->getId(),'codigo'=>$f->getCodigo(),'nome'=>$f->getNome(),'tipo'=>$f->getTipo(),'status'=>$f->getStatus()]; }
}
