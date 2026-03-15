<?php
declare(strict_types=1);
namespace App\Bpo\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreateComentarioTituloRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $empresaId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $unidadeId = null; #[Assert\NotBlank] public string $comentario = ''; }
