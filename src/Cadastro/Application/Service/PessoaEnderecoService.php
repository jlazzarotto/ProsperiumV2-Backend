<?php

declare(strict_types=1);

namespace App\Cadastro\Application\Service;

use App\Cadastro\Application\DTO\CreatePessoaEnderecoRequest;
use App\Cadastro\Application\DTO\UpdatePessoaEnderecoRequest;
use App\Cadastro\Domain\Entity\PessoaEndereco;
use App\Cadastro\Domain\Repository\PessoaEnderecoRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;

final class PessoaEnderecoService
{
    public function __construct(
        private readonly PessoaEnderecoRepositoryInterface $repo,
        private readonly PessoaService $pessoaService,
        private readonly RequestValidator $validator,
        private readonly TransactionRunnerInterface $tx,
        private readonly AuditoriaLogger $audit,
    ) {
    }

    public function create(CreatePessoaEnderecoRequest $r, ?int $currentUserId = null): PessoaEndereco
    {
        $this->validator->validate($r);

        $pessoa = $this->pessoaService->getById((int) $r->pessoaId);

        return $this->tx->run(function () use ($r, $pessoa, $currentUserId): PessoaEndereco {
            $e = new PessoaEndereco(
                $pessoa->getCompany(),
                $pessoa,
                $r->tipoEndereco,
                $r->logradouro,
                $r->cidade,
                $r->pais,
                $r->principal,
                $r->numero,
                $r->complemento,
                $r->bairro,
                $r->uf,
                $r->cep,
                $currentUserId,
            );
            $this->repo->save($e);
            $this->audit->log((int) $pessoa->getCompany()->getId(), 'pessoa_endereco', 'cadastro.pessoa_endereco.created', ['enderecoId' => $e->getId(), 'pessoaId' => $pessoa->getId()]);

            return $e;
        });
    }

    public function update(int $id, UpdatePessoaEnderecoRequest $r, ?int $currentUserId = null): PessoaEndereco
    {
        $this->validator->validate($r);

        $endereco = $this->getById($id);

        return $this->tx->run(function () use ($endereco, $r, $currentUserId): PessoaEndereco {
            $endereco->update(
                $r->tipoEndereco,
                $r->logradouro,
                $r->cidade,
                $r->pais,
                $r->principal,
                $r->numero,
                $r->complemento,
                $r->bairro,
                $r->uf,
                $r->cep,
                $currentUserId,
            );
            $this->repo->save($endereco);
            $this->audit->log((int) $endereco->getCompany()->getId(), 'pessoa_endereco', 'cadastro.pessoa_endereco.updated', ['enderecoId' => $endereco->getId()]);

            return $endereco;
        });
    }

    public function getById(int $id): PessoaEndereco
    {
        $endereco = $this->repo->findById($id);
        if ($endereco === null) {
            throw new ResourceNotFoundException('Endereço não encontrado.');
        }

        return $endereco;
    }

    /**
     * @return list<PessoaEndereco>
     */
    public function listByPessoa(int $pessoaId): array
    {
        return $this->repo->listByPessoa($pessoaId);
    }

    public function delete(int $id, ?int $currentUserId = null): void
    {
        $endereco = $this->getById($id);

        $this->tx->run(function () use ($endereco, $currentUserId): void {
            $this->repo->softDelete($endereco);
            $this->audit->log((int) $endereco->getCompany()->getId(), 'pessoa_endereco', 'cadastro.pessoa_endereco.deleted', ['enderecoId' => $endereco->getId()]);
        });
    }
}
