<?php

declare(strict_types=1);

namespace App\Identity\Domain\Repository;

interface ModuloRepositoryInterface
{
    /**
     * @return list<\App\Identity\Domain\Entity\Modulo>
     */
    public function listAllActive(): array;

    /**
     * @return list<\App\Identity\Domain\Entity\Modulo>
     */
    public function listMenuEntries(): array;
}
