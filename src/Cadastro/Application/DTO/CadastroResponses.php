<?php
declare(strict_types=1);
namespace App\Cadastro\Application\DTO;
use App\Cadastro\Domain\Entity\CategoriaFinanceira;
use App\Cadastro\Domain\Entity\CentroCusto;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Cadastro\Domain\Entity\FormaPagamento;
use App\Cadastro\Domain\Entity\Pessoa;
final class CadastroResponses
{
    public static function pessoa(Pessoa $p): array { return ['id'=>$p->getId(),'companyId'=>$p->getCompany()->getId(),'empresaId'=>$p->getEmpresa()?->getId(),'nome'=>$p->getNome(),'documento'=>$p->getDocumento(),'classificacao'=>$p->getClassificacao(),'status'=>$p->getStatus()]; }
    public static function categoria(CategoriaFinanceira $c): array { return ['id'=>$c->getId(),'companyId'=>$c->getCompany()->getId(),'parentId'=>$c->getParent()?->getId(),'codigo'=>$c->getCodigo(),'nome'=>$c->getNome(),'tipo'=>$c->getTipo(),'status'=>$c->getStatus()]; }
    public static function centro(CentroCusto $c): array { return ['id'=>$c->getId(),'companyId'=>$c->getCompany()->getId(),'parentId'=>$c->getParent()?->getId(),'codigo'=>$c->getCodigo(),'nome'=>$c->getNome(),'status'=>$c->getStatus()]; }
    public static function conta(ContaFinanceira $c): array { return ['id'=>$c->getId(),'companyId'=>$c->getCompany()->getId(),'empresaId'=>$c->getEmpresa()->getId(),'unidadeId'=>$c->getUnidade()?->getId(),'codigo'=>$c->getCodigo(),'nome'=>$c->getNome(),'tipo'=>$c->getTipo(),'status'=>$c->getStatus()]; }
    public static function forma(FormaPagamento $f): array { return ['id'=>$f->getId(),'companyId'=>$f->getCompany()->getId(),'codigo'=>$f->getCodigo(),'nome'=>$f->getNome(),'tipo'=>$f->getTipo(),'status'=>$f->getStatus()]; }
}
