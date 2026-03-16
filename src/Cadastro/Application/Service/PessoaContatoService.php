<?php

declare(strict_types=1);

namespace App\Cadastro\Application\Service;

use App\Cadastro\Application\DTO\CreatePessoaContatoRequest;
use App\Cadastro\Application\DTO\UpdatePessoaContatoRequest;
use App\Cadastro\Domain\Entity\PessoaContato;
use App\Cadastro\Domain\Repository\PessoaContatoRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;

final class PessoaContatoService
{
    public function __construct(
        private readonly PessoaContatoRepositoryInterface $repo,
        private readonly PessoaService $pessoaService,
        private readonly RequestValidator $validator,
        private readonly TransactionRunnerInterface $tx,
        private readonly AuditoriaLogger $audit,
    ) {
    }

    public function create(CreatePessoaContatoRequest $r, ?int $currentUserId = null): PessoaContato
    {
        $this->validator->validate($r);

        $pessoa = $this->pessoaService->getById((int) $r->pessoaId);

        return $this->tx->run(function () use ($r, $pessoa, $currentUserId): PessoaContato {
            $c = new PessoaContato(
                $pessoa->getCompany(),
                $pessoa,
                $r->nomeContato,
                $r->principal,
                $r->cargo,
                $r->email,
                $r->telefone,
                $currentUserId,
            );
            $this->repo->save($c);
            $this->audit->log((int) $pessoa->getCompany()->getId(), 'pessoa_contato', 'cadastro.pessoa_contato.created', ['contatoId' => $c->getId(), 'pessoaId' => $pessoa->getId()]);

            return $c;
        });
    }

    public function update(int $id, UpdatePessoaContatoRequest $r, ?int $currentUserId = null): PessoaContato
    {
        $this->validator->validate($r);

        $contato = $this->getById($id);

        return $this->tx->run(function () use ($contato, $r, $currentUserId): PessoaContato {
            $contato->update(
                $r->nomeContato,
                $r->principal,
                $r->cargo,
                $r->email,
                $r->telefone,
                $currentUserId,
            );
            $this->repo->save($contato);
            $this->audit->log((int) $contato->getCompany()->getId(), 'pessoa_contato', 'cadastro.pessoa_contato.updated', ['contatoId' => $contato->getId()]);

            return $contato;
        });
    }

    public function getById(int $id): PessoaContato
    {
        $contato = $this->repo->findById($id);
        if ($contato === null) {
            throw new ResourceNotFoundException('Contato não encontrado.');
        }

        return $contato;
    }

    /**
     * @return list<PessoaContato>
     */
    public function listByPessoa(int $pessoaId): array
    {
        return $this->repo->listByPessoa($pessoaId);
    }

    public function delete(int $id, ?int $currentUserId = null): void
    {
        $contato = $this->getById($id);

        $this->tx->run(function () use ($contato, $currentUserId): void {
            $this->repo->softDelete($contato);
            $this->audit->log((int) $contato->getCompany()->getId(), 'pessoa_contato', 'cadastro.pessoa_contato.deleted', ['contatoId' => $contato->getId()]);
        });
    }
}
