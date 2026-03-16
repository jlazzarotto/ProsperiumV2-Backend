<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Repository;

use App\Cadastro\Domain\Entity\PessoaContato;

interface PessoaContatoRepositoryInterface
{
    public function save(PessoaContato $contato): void;

    public function findById(int $id): ?PessoaContato;

    public function softDelete(PessoaContato $contato): void;

    /**
     * @return list<PessoaContato>
     */
    public function listByPessoa(int $pessoaId): array;
}
