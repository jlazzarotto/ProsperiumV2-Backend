<?php
declare(strict_types=1);
namespace App\Bpo\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreateRegraAutomaticaClassificacaoRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId = null; #[Assert\Positive] public ?int $empresaId = null; #[Assert\Positive] public ?int $unidadeId = null; #[Assert\Positive] public ?int $categoriaFinanceiraId = null; #[Assert\Positive] public ?int $centroCustoId = null; #[Assert\NotBlank] public string $descricaoContains = ''; public bool $acaoNotificacao = true; #[Assert\Choice(choices: ['active','inactive'])] public string $status = 'active'; }
