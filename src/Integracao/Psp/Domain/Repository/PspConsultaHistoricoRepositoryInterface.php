<?php

declare(strict_types=1);

namespace App\Integracao\Psp\Domain\Repository;

use App\Integracao\Psp\Domain\Entity\PspConsultaHistorico;

interface PspConsultaHistoricoRepositoryInterface
{
    public function save(PspConsultaHistorico $historico): void;

    /** @return list<PspConsultaHistorico> */
    public function listRecent(?int $companyId = null, ?string $endpointKey = null, int $limit = 30): array;
}
