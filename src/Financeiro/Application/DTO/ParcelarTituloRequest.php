<?php
declare(strict_types=1);
namespace App\Financeiro\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class ParcelarTituloRequest { /** @var list<array{numero:int,valor:string,vencimento:string}> */ #[Assert\NotBlank] #[Assert\Count(min:1)] public array $parcelas=[]; }
