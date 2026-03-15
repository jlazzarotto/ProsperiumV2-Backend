<?php
declare(strict_types=1);
namespace App\Cobranca\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreatePixCobrancaRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $empresaId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $unidadeId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $parcelaId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $contaFinanceiraId = null; #[Assert\NotBlank] public string $chavePix = ''; #[Assert\NotBlank] public string $valor = '0.00'; #[Assert\Positive] public int $expiracaoSegundos = 3600; public ?string $txid = null; public ?string $qrCode = null; public ?string $copiaCola = null; }
