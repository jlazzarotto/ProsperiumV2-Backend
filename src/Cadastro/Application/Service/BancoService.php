<?php

declare(strict_types=1);

namespace App\Cadastro\Application\Service;

use App\Cadastro\Domain\Repository\BancoRepositoryInterface;

final class BancoService
{
    public function __construct(private readonly BancoRepositoryInterface $repo)
    {
    }

    public function list(?string $status = 'active'): array
    {
        return $this->repo->listAll($status);
    }
}
