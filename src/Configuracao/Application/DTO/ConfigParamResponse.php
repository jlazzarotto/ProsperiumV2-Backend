<?php

declare(strict_types=1);

namespace App\Configuracao\Application\DTO;

use App\Configuracao\Domain\Entity\ConfigParam;

final class ConfigParamResponse
{
    public static function fromEntity(ConfigParam $p): array
    {
        return [
            'id' => $p->getId(),
            'companyId' => $p->getCompany()->getId(),
            'name' => $p->getName(),
            'type' => $p->getType() ?? '',
            'value' => $p->getValue(),
            'description' => $p->getDescription() ?? '',
            'status' => $p->getStatus(),
            'restrict' => $p->getRestrict(),
            'createdAt' => $p->getCreatedAt()->format(\DATE_ATOM),
            'updatedAt' => $p->getUpdatedAt()->format(\DATE_ATOM),
        ];
    }
}
