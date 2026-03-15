<?php
declare(strict_types=1);
namespace App\Tests\Contabil\Domain\Entity;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Contabil\Domain\Entity\SnapshotFluxoCaixa;
use PHPUnit\Framework\TestCase;
final class SnapshotFluxoCaixaTest extends TestCase
{
    public function testAtualizarValoresSubstituiSaldos(): void
    {
        $company=new Company('Prosperium'); $empresa=new Empresa($company,'Empresa',null,'12345678000199'); $unidade=new UnidadeNegocio($company,'Operacao','OP'); $snapshot=new SnapshotFluxoCaixa($company,$empresa,$unidade,new \DateTimeImmutable('2026-03-14'),'0.00','10.00','5.00','5.00');
        $snapshot->atualizarValores('5.00','20.00','10.00','15.00');
        self::assertSame('5.00',$snapshot->getSaldoInicial());
        self::assertSame('15.00',$snapshot->getSaldoFinal());
    }
}
