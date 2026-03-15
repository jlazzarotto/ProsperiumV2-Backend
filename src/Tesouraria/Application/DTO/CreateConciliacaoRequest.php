<?php
declare(strict_types=1);
namespace App\Tesouraria\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreateConciliacaoRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId=null; #[Assert\NotNull] #[Assert\Positive] public ?int $extratoBancarioId=null; #[Assert\Positive] public ?int $movimentoFinanceiroId=null; #[Assert\Positive] public ?int $baixaId=null; #[Assert\NotBlank] #[Assert\Choice(choices:['manual','semi_automatica'])] public string $modo='manual'; }
