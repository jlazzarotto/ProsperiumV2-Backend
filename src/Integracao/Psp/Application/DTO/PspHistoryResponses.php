<?php

declare(strict_types=1);

namespace App\Integracao\Psp\Application\DTO;

use App\Integracao\Psp\Domain\Entity\PspConsultaHistorico;

final class PspHistoryResponses
{
    /**
     * @return array<string, mixed>
     */
    public static function item(PspConsultaHistorico $historico): array
    {
        return [
            'id' => $historico->getId(),
            'companyId' => $historico->getCompanyId(),
            'userId' => $historico->getUserId(),
            'endpointKey' => $historico->getEndpointKey(),
            'request' => $historico->getRequestJson(),
            'response' => $historico->getResponseJson(),
            'success' => $historico->isSuccess(),
            'durationMs' => $historico->getDurationMs(),
            'errorMessage' => $historico->getErrorMessage(),
            'createdAt' => $historico->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
