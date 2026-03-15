<?php
declare(strict_types=1);
namespace App\Tests\Bpo\Domain\Entity;
use App\Bpo\Domain\Entity\TarefaOperacionalBpo;
use App\Cadastro\Domain\Entity\ContaFinanceira;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Financeiro\Domain\Entity\Titulo;
use App\Identity\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
final class TarefaOperacionalBpoTest extends TestCase
{
    public function testConstrutorMantemContextoInformado(): void
    {
        $company=new Company('Prosperium'); $empresa=new Empresa($company,'Empresa',null,'12345678000199'); $unidade=new UnidadeNegocio($company,'Operacao','OP'); $pessoa=new Pessoa($company,null,'Cliente',null,'cliente'); $conta=new ContaFinanceira($company,$empresa,null,'CX1','Caixa','caixa'); $titulo=new Titulo($company,$empresa,$unidade,$pessoa,'receber',null,'100.00',new \DateTimeImmutable('2026-03-14'),null,$conta); $responsavel=new User('Resp','resp@prosperium.test','hash'); $tarefa=new TarefaOperacionalBpo($company,$empresa,$unidade,$titulo,$responsavel,'conferencia','Validar documento','alta');
        self::assertSame('conferencia',$tarefa->getTipo());
        self::assertSame('alta',$tarefa->getPrioridade());
        self::assertSame('aberta',$tarefa->getStatus());
    }
}
