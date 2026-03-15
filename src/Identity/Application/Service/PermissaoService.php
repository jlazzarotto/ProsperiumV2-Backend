<?php

declare(strict_types=1);

namespace App\Identity\Application\Service;

use App\Identity\Domain\Repository\PermissaoRepositoryInterface;

final class PermissaoService
{
    public function __construct(private readonly PermissaoRepositoryInterface $permissaoRepository)
    {
    }

    public function list(?string $moduloCodigo = null): array
    {
        return $this->permissaoRepository->listAll($moduloCodigo);
    }
}
