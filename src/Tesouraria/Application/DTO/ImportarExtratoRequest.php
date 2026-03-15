<?php
declare(strict_types=1);
namespace App\Tesouraria\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class ImportarExtratoRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId=null; #[Assert\NotNull] #[Assert\Positive] public ?int $empresaId=null; #[Assert\NotNull] #[Assert\Positive] public ?int $unidadeId=null; #[Assert\NotNull] #[Assert\Positive] public ?int $contaFinanceiraId=null; /** @var list<array{codigoExterno?:string,dataMovimento:string,valor:string,tipo:string,descricao:string}> */ #[Assert\NotBlank] #[Assert\Count(min:1)] public array $itens=[]; }
