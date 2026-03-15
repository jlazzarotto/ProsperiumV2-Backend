<?php
declare(strict_types=1);
namespace App\Tests\Bpo\Domain\Entity;
use App\Bpo\Domain\Entity\AprovacaoTitulo;
use App\Bpo\Domain\Entity\AprovacaoTituloItem;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Financeiro\Domain\Entity\Titulo;
use App\Identity\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
final class AprovacaoTituloItemTest extends TestCase
{
    public function testAprovarAtualizaStatusDoItem(): void
    {
        $company=new Company('Prosperium'); $empresa=new Empresa($company,'Empresa',null,'12345678000199'); $unidade=new UnidadeNegocio($company,'Operacao','OP'); $pessoa=new Pessoa($company,null,'Cliente',null,'cliente'); $conta=new ContaFinanceira($company,$empresa,null,'CX1','Caixa','caixa'); $titulo=new Titulo($company,$empresa,$unidade,$pessoa,'receber',null,'100.00',new \DateTimeImmutable('2026-03-14'),null,$conta); $solicitante=new User('Solicitante','sol@prosperium.test','hash'); $aprovador=new User('Aprovador','apr@prosperium.test','hash'); $aprovacao=new AprovacaoTitulo($company,$empresa,$unidade,$titulo,$solicitante,'aprovacao_titulo_receber','100.00'); $item=new AprovacaoTituloItem($aprovacao,$aprovador,1,'100.00');
        $item->aprovar('ok');
        self::assertSame('aprovado',$item->getStatus());
    }
}
