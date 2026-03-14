<?php

declare(strict_types=1);

namespace App\Module\Empresa\Application\DTO;

final class UpdateEmpresaDTO
{
    public ?string $razaoSocial = null;
    public ?string $nomeFantasia = null;
    public ?string $cnpj = null;
}
