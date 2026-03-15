<?php
declare(strict_types=1);
namespace App\Tests\Cobranca\Domain\Entity;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Cobranca\Domain\Entity\PixCobranca;
use App\Financeiro\Domain\Entity\Titulo;
use App\Financeiro\Domain\Entity\TituloParcela;
use PHPUnit\Framework\TestCase;
final class PixCobrancaTest extends TestCase
{
    public function testMarcarRecebidaAtualizaStatus(): void
    {
        $company=new Company('Prosperium'); $empresa=new Empresa($company,'Empresa',null,'12345678000199'); $unidade=new UnidadeNegocio($company,'Operacao','OP'); $pessoa=new Pessoa($company,null,'Cliente',null,'cliente'); $conta=new ContaFinanceira($company,$empresa,null,'CX1','Caixa','caixa'); $titulo=new Titulo($company,$empresa,$unidade,$pessoa,'receber',null,'100.00',new \DateTimeImmutable('2026-03-14'),null,$conta); $parcela=new TituloParcela($titulo,$company,$empresa,$unidade,1,'100.00',new \DateTimeImmutable('2026-04-10')); $pix=new PixCobranca($company,$empresa,$unidade,$parcela,$conta,'TX123','chave@pix','100.00',3600);
        $pix->marcarRecebida();
        self::assertSame('recebida',$pix->getStatus());
    }
}
