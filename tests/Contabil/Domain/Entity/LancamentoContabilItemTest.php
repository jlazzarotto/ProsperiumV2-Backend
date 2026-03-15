<?php
declare(strict_types=1);
namespace App\Tests\Contabil\Domain\Entity;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Contabil\Domain\Entity\ContaContabil;
use App\Contabil\Domain\Entity\LancamentoContabil;
use App\Contabil\Domain\Entity\LancamentoContabilItem;
use App\Financeiro\Domain\Entity\Titulo;
use PHPUnit\Framework\TestCase;
final class LancamentoContabilItemTest extends TestCase
{
    public function testConstrutorMantemNaturezaEValor(): void
    {
        $company=new Company('Prosperium'); $empresa=new Empresa($company,'Empresa',null,'12345678000199'); $unidade=new UnidadeNegocio($company,'Operacao','OP'); $pessoa=new Pessoa($company,null,'Cliente',null,'cliente'); $contaFinanceira=new ContaFinanceira($company,$empresa,null,'CX1','Caixa','caixa'); $titulo=new Titulo($company,$empresa,$unidade,$pessoa,'receber',null,'100.00',new \DateTimeImmutable('2026-03-14'),null,$contaFinanceira); $contaContabil=new ContaContabil($company,null,'1.1.1','Caixa','ativo'); $lancamento=new LancamentoContabil($company,$empresa,$unidade,$titulo,new \DateTimeImmutable('2026-03-14'),'Abertura');
        $item=new LancamentoContabilItem($lancamento,$contaContabil,'debito','100.00');
        self::assertSame('debito',$item->getNatureza());
        self::assertSame('100.00',$item->getValor());
    }
}
