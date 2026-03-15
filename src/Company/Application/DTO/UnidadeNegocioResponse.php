<?php

declare(strict_types=1);

namespace App\Company\Application\DTO;

use App\Company\Domain\Entity\UnidadeNegocio;

final class UnidadeNegocioResponse
{
    public static function fromEntity(UnidadeNegocio $unidadeNegocio): array
    {
        return [
            'id' => $unidadeNegocio->getId(),
            'companyId' => $unidadeNegocio->getCompany()->getId(),
            'nome' => $unidadeNegocio->getNome(),
            'abreviatura' => $unidadeNegocio->getAbreviatura(),
            'status' => $unidadeNegocio->getStatus(),
        ];
    }
}
