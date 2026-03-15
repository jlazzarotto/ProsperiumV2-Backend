<?php
declare(strict_types=1);
namespace App\Cadastro\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreatePessoaRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId = null; #[Assert\Positive] public ?int $empresaId = null; #[Assert\NotBlank] #[Assert\Length(max:255)] public string $nome=''; #[Assert\Length(max:40)] public ?string $documento=null; #[Assert\NotBlank] #[Assert\Choice(choices:['cliente','fornecedor','ambos'])] public string $classificacao='ambos'; #[Assert\NotBlank] #[Assert\Choice(choices:['active','inactive'])] public string $status='active'; }
