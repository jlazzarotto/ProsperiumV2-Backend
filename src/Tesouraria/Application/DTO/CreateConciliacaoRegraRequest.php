<?php
declare(strict_types=1);
namespace App\Tesouraria\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreateConciliacaoRegraRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId=null; #[Assert\Positive] public ?int $empresaId=null; #[Assert\Positive] public ?int $unidadeId=null; #[Assert\Positive] public ?int $contaFinanceiraId=null; #[Assert\NotBlank] #[Assert\Length(max:255)] public string $descricaoContains=''; #[Assert\NotBlank] #[Assert\Choice(choices:['credito','debito'])] public string $tipoMovimentoSugerido='credito'; #[Assert\NotBlank] #[Assert\Choice(choices:['sugerir_movimento'])] public string $aplicacao='sugerir_movimento'; #[Assert\NotBlank] #[Assert\Choice(choices:['active','inactive'])] public string $status='active'; }
