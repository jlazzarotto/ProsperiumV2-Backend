<?php

declare(strict_types=1);

namespace App\Module\Empresa\Application\DTO;

final class CreateEmpresaDTO
{
    public string $razaoSocial;
    public ?string $nomeFantasia = null;
    public ?string $cnpj = null;
}
