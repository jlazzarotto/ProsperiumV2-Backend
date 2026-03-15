<?php

declare(strict_types=1);

namespace App\Company\Application\DTO;

use App\Company\Domain\Entity\Empresa;

final class EmpresaResponse
{
    public static function fromEntity(Empresa $empresa): array
    {
        return [
            'id' => $empresa->getId(),
            'companyId' => $empresa->getCompany()->getId(),
            'razaoSocial' => $empresa->getRazaoSocial(),
            'nomeFantasia' => $empresa->getNomeFantasia(),
            'cnpj' => $empresa->getCnpj(),
            'status' => $empresa->getStatus(),
        ];
    }
}
