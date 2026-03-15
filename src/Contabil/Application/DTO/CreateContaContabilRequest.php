<?php
declare(strict_types=1);
namespace App\Contabil\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreateContaContabilRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId = null; #[Assert\Positive] public ?int $parentId = null; #[Assert\NotBlank] public string $codigo = ''; #[Assert\NotBlank] public string $nome = ''; #[Assert\Choice(choices: ['ativo','passivo','receita','despesa','patrimonio'])] public string $tipo = 'ativo'; #[Assert\Choice(choices: ['active','inactive'])] public string $status = 'active'; }
