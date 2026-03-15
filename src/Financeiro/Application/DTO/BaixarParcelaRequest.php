<?php
declare(strict_types=1);
namespace App\Financeiro\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class BaixarParcelaRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId=null; #[Assert\NotNull] #[Assert\Positive] public ?int $contaFinanceiraId=null; #[Assert\NotBlank] public string $valor='0.00'; #[Assert\NotBlank] public string $dataPagamento=''; public ?string $observacoes=null; }
