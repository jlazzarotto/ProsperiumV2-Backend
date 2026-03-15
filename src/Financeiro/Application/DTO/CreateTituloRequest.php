<?php
declare(strict_types=1);
namespace App\Financeiro\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreateTituloRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId=null; #[Assert\NotNull] #[Assert\Positive] public ?int $empresaId=null; #[Assert\NotNull] #[Assert\Positive] public ?int $unidadeId=null; #[Assert\NotNull] #[Assert\Positive] public ?int $pessoaId=null; #[Assert\Positive] public ?int $contaFinanceiraId=null; #[Assert\NotBlank] #[Assert\Choice(choices:['pagar','receber'])] public string $tipo='pagar'; #[Assert\Length(max:100)] public ?string $numeroDocumento=null; #[Assert\NotBlank] public string $valorTotal='0.00'; #[Assert\NotBlank] public string $dataEmissao=''; public ?string $observacoes=null; public ?string $primeiroVencimento=null; }
