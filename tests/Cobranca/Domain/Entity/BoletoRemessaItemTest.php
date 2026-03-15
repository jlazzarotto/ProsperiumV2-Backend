<?php
declare(strict_types=1);
namespace App\Tests\Cobranca\Domain\Entity;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Cobranca\Domain\Entity\BoletoRemessa;
use App\Cobranca\Domain\Entity\BoletoRemessaItem;
use App\Financeiro\Domain\Entity\Titulo;
use App\Financeiro\Domain\Entity\TituloParcela;
use App\Cadastro\Domain\Entity\Pessoa;
use PHPUnit\Framework\TestCase;
final class BoletoRemessaItemTest extends TestCase
{
    public function testRegistrarRetornoAtualizaStatusDoItemERemessa(): void
    {
        $company=new Company('Prosperium'); $empresa=new Empresa($company,'Empresa',null,'12345678000199'); $unidade=new UnidadeNegocio($company,'Operacao','OP'); $pessoa=new Pessoa($company,null,'Cliente',null,'cliente'); $conta=new ContaFinanceira($company,$empresa,null,'CX1','Caixa','caixa'); $titulo=new Titulo($company,$empresa,$unidade,$pessoa,'receber',null,'100.00',new \DateTimeImmutable('2026-03-14'),null,$conta); $parcela=new TituloParcela($titulo,$company,$empresa,$unidade,1,'100.00',new \DateTimeImmutable('2026-04-10')); $remessa=new BoletoRemessa($company,$empresa,$unidade,$conta,'BRM-1','Banco Teste'); $item=new BoletoRemessaItem($remessa,$parcela,'123456','100.00',new \DateTimeImmutable('2026-04-10'));
        $item->registrarRetorno('06','liquidado',new \DateTimeImmutable('2026-04-12'));
        self::assertSame('liquidado',$item->getStatus());
        self::assertSame('06',$item->getOcorrenciaRetorno());
        self::assertSame('processada',$remessa->getStatus());
    }
}
