<?php
declare(strict_types=1);
namespace App\Cobranca\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class RegisterPixWebhookRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId = null; #[Assert\Positive] public ?int $empresaId = null; #[Assert\Positive] public ?int $unidadeId = null; #[Assert\Positive] public ?int $pixCobrancaId = null; public ?string $txid = null; #[Assert\NotBlank] public string $tipoEvento = ''; public array $payload = []; public ?string $endToEndId = null; public ?string $valor = null; public ?string $recebidoEm = null; }
