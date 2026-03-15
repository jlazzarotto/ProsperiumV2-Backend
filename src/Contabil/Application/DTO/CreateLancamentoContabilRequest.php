<?php
declare(strict_types=1);
namespace App\Contabil\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreateLancamentoContabilRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $empresaId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $unidadeId = null; #[Assert\Positive] public ?int $tituloId = null; #[Assert\NotBlank] public string $dataLancamento = ''; #[Assert\NotBlank] public string $historico = ''; /** @var list<array{contaContabilId:int,natureza:string,valor:string}> */ #[Assert\Count(min: 2)] public array $itens = []; }
