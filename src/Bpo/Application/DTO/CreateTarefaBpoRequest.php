<?php
declare(strict_types=1);
namespace App\Bpo\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreateTarefaBpoRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $empresaId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $unidadeId = null; #[Assert\Positive] public ?int $tituloId = null; #[Assert\Positive] public ?int $responsavelUserId = null; #[Assert\NotBlank] public string $tipo = ''; #[Assert\NotBlank] public string $descricao = ''; #[Assert\Choice(choices: ['baixa','media','alta'])] public string $prioridade = 'media'; public ?string $prazoEm = null; }
