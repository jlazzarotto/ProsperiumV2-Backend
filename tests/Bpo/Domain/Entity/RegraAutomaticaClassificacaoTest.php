<?php
declare(strict_types=1);
namespace App\Tests\Bpo\Domain\Entity;
use App\Bpo\Domain\Entity\RegraAutomaticaClassificacao;
use App\Company\Domain\Entity\Company;
use PHPUnit\Framework\TestCase;
final class RegraAutomaticaClassificacaoTest extends TestCase
{
    public function testMatchesRetornaTrueQuandoTextoContemExpressao(): void
    {
        $regra=new RegraAutomaticaClassificacao(new Company('Prosperium'),null,null,null,null,'energia');
        self::assertTrue($regra->matches('Conta de energia março'));
        self::assertFalse($regra->matches('Pagamento de aluguel'));
    }
}
