<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Repository;

use App\Cadastro\Domain\Entity\PessoaEndereco;

interface PessoaEnderecoRepositoryInterface
{
    public function save(PessoaEndereco $endereco): void;

    public function findById(int $id): ?PessoaEndereco;

    public function softDelete(PessoaEndereco $endereco): void;

    /**
     * @return list<PessoaEndereco>
     */
    public function listByPessoa(int $pessoaId): array;
}
