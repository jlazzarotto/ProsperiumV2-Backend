<?php
declare(strict_types=1);
namespace App\Cobranca\Application\DTO;
use Symfony\Component\Validator\Constraints as Assert;
final class ImportarBoletoRetornoRequest { #[Assert\NotNull] #[Assert\Positive] public ?int $companyId = null; #[Assert\Positive] public ?int $remessaId = null; /** @var list<array{nossoNumero:string,codigoOcorrencia:string,descricao?:string,valorRecebido?:string,dataOcorrencia?:string,linhaOriginal?:string}> */ #[Assert\Count(min: 1)] public array $itens = []; }
