<?php
declare(strict_types=1);
namespace App\Cobranca\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreateBoletoRemessaRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $empresaId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $unidadeId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $contaFinanceiraId = null; #[Assert\NotBlank] #[Assert\Length(max: 100)] public string $banco = ''; /** @var list<int> */ #[Assert\Count(min: 1)] public array $parcelaIds = []; }
