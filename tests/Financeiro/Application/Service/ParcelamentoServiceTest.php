<?php
declare(strict_types=1);
namespace App\Tests\Financeiro\Application\Service;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Financeiro\Domain\Entity\Titulo;
use App\Financeiro\Domain\Service\ParcelamentoService;
use PHPUnit\Framework\TestCase;
final class ParcelamentoServiceTest extends TestCase
{
    public function testGerarParcelasValidaSoma(): void
    {
        $company=new Company('Prosperium'); $empresa=new Empresa($company,'Empresa',null,'12345678000199'); $unidade=new UnidadeNegocio($company,'Operacao','OP'); $pessoa=new Pessoa($company,null,'Cliente',null,'cliente'); $titulo=new Titulo($company,$empresa,$unidade,$pessoa,'receber',null,'100.00',new \DateTimeImmutable('2026-03-14'));
        $service=new ParcelamentoService();
        $parcelas=$service->gerarParcelas($titulo,[['numero'=>1,'valor'=>'40.00','vencimento'=>new \DateTimeImmutable('2026-04-10')],['numero'=>2,'valor'=>'60.00','vencimento'=>new \DateTimeImmutable('2026-05-10')]]);
        self::assertCount(2,$parcelas);
        self::assertSame('100.00',number_format((float)$parcelas[0]->getValor()+(float)$parcelas[1]->getValor(),2,'.',''));
    }
}
