<?php
declare(strict_types=1);
namespace App\Bpo\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class CreateAprovacaoTituloRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $empresaId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $unidadeId = null; #[Assert\NotNull] #[Assert\Positive] public ?int $tituloId = null; /** @var list<int> */ #[Assert\Count(min: 1)] public array $aprovadorIds = []; public ?string $tipoOperacao = null; }
