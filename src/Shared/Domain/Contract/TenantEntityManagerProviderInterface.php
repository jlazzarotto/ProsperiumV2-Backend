<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contract;

use Doctrine\ORM\EntityManagerInterface;

interface TenantEntityManagerProviderInterface
{
    /**
     * Retorna o EntityManager do tenant ativo no request.
     * Lança exceção se nenhum tenant estiver resolvido.
     */
    public function getEntityManager(): EntityManagerInterface;

    /**
     * Verifica se o tenant plane está disponível no request atual.
     */
    public function isAvailable(): bool;
}
