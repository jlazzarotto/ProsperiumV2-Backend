<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Repository;

use App\Cadastro\Domain\Entity\Pessoa;

interface PessoaRepositoryInterface
{
    public function save(Pessoa $pessoa): void;

    public function findById(int $id): ?Pessoa;

    public function existsByCompanyAndDocumento(int $companyId, string $documento, ?int $excludeId = null): bool;

    public function softDelete(Pessoa $pessoa): void;

    /**
     * @return list<Pessoa>
     */
    public function listAll(int $companyId, ?string $tipoPessoa = null, ?string $status = null): array;
}
