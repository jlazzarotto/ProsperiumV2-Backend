<?php
declare(strict_types=1);
namespace App\Tests\Tesouraria\Domain\Service;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Financeiro\Domain\Entity\MovimentoFinanceiro;
use App\Tesouraria\Domain\Entity\ConciliacaoRegra;
use App\Tesouraria\Domain\Entity\ExtratoBancario;
use App\Tesouraria\Domain\Service\SugestaoConciliacaoService;
use PHPUnit\Framework\TestCase;
final class SugestaoConciliacaoServiceTest extends TestCase
{
    public function testSugereMovimentoPorDescricaoEValor(): void
    {
        $company=new Company('Prosperium'); $empresa=new Empresa($company,'Empresa',null,'12345678000199'); $unidade=new UnidadeNegocio($company,'Operacao','OP'); $conta=new ContaFinanceira($company,$empresa,null,'CX1','Caixa','caixa');
        $extrato=new ExtratoBancario($company,$empresa,$unidade,$conta,'ABC',new \DateTimeImmutable('2026-03-14'),'100.00','credito','PIX CLIENTE XPTO');
        $regra=new ConciliacaoRegra($company,null,null,$conta,'pix','credito','sugerir_movimento');
        $movimento=new MovimentoFinanceiro($company,$empresa,$unidade,$conta,null,null,'credito','100.00',new \DateTimeImmutable('2026-03-14'),'PIX CLIENTE XPTO');
        $idProp=new \ReflectionProperty($movimento,'id'); $idProp->setValue($movimento,5);
        $regraIdProp=new \ReflectionProperty($regra,'id'); $regraIdProp->setValue($regra,7);
        $service=new SugestaoConciliacaoService();
        $result=$service->sugerir($extrato,[$regra],[$movimento]);
        self::assertSame(7,$result['regraId']);
        self::assertSame(5,$result['movimentoId']);
        self::assertSame(100,$result['score']);
    }
}
