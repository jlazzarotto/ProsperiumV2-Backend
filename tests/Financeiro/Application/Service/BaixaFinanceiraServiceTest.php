<?php
declare(strict_types=1);
namespace App\Tests\Financeiro\Application\Service;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Financeiro\Domain\Entity\Titulo;
use App\Financeiro\Domain\Entity\TituloParcela;
use App\Financeiro\Domain\Service\BaixaFinanceiraService;
use PHPUnit\Framework\TestCase;
final class BaixaFinanceiraServiceTest extends TestCase
{
    public function testBaixarParcelaAtualizaSaldoAberto(): void
    {
        $company=new Company('Prosperium'); $empresa=new Empresa($company,'Empresa',null,'12345678000199'); $unidade=new UnidadeNegocio($company,'Operacao','OP'); $pessoa=new Pessoa($company,null,'Cliente',null,'cliente'); $conta=new ContaFinanceira($company,$empresa,null,'CX1','Caixa','caixa'); $titulo=new Titulo($company,$empresa,$unidade,$pessoa,'receber',null,'100.00',new \DateTimeImmutable('2026-03-14'),null,$conta); $parcela=new TituloParcela($titulo,$company,$empresa,$unidade,1,'100.00',new \DateTimeImmutable('2026-04-10'));
        $service=new BaixaFinanceiraService();
        $result=$service->baixar($parcela,$conta,'100.00',new \DateTimeImmutable('2026-04-10'),'Baixa integral');
        self::assertSame('0.00',$parcela->getValorAberto());
        self::assertSame('liquidado',$parcela->getStatus());
        self::assertSame('100.00',$result['baixa']->getValor());
    }
}
