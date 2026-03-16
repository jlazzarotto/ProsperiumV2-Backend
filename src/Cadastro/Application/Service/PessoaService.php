<?php

declare(strict_types=1);

namespace App\Cadastro\Application\Service;

use App\Cadastro\Application\DTO\CreatePessoaRequest;
use App\Cadastro\Application\DTO\UpdatePessoaRequest;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Cadastro\Domain\Repository\PessoaRepositoryInterface;
use App\Company\Domain\Repository\TenantCompanyContextRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;

final class PessoaService
{
    public function __construct(
        private readonly PessoaRepositoryInterface $repo,
        private readonly TenantCompanyContextRepositoryInterface $companyRepo,
        private readonly RequestValidator $validator,
        private readonly TransactionRunnerInterface $tx,
        private readonly AuditoriaLogger $audit,
    ) {
    }

    public function create(CreatePessoaRequest $r, ?int $currentUserId = null): Pessoa
    {
        $this->validator->validate($r);

        $company = $this->companyRepo->findById((int) $r->companyId);
        if ($company === null) {
            throw new ResourceNotFoundException('Company não encontrada.');
        }

        $documento = $this->normalizeDocumento($r->documento);

        if ($documento !== null && $this->repo->existsByCompanyAndDocumento((int) $r->companyId, $documento)) {
            throw new ValidationException(['documento' => ['Já existe uma pessoa com este documento na company informada.']]);
        }

        return $this->tx->run(function () use ($r, $company, $documento, $currentUserId): Pessoa {
            $p = new Pessoa(
                $company,
                $r->tipoPessoa,
                $r->nomeRazao,
                $r->nomeFantasia,
                $documento,
                $r->inscricaoEstadual,
                $r->emailPrincipal,
                $r->telefonePrincipal,
                $r->status,
                $currentUserId,
            );
            $this->repo->save($p);
            $this->audit->log((int) $company->getId(), 'pessoa', 'cadastro.pessoa.created', ['pessoaId' => $p->getId()]);

            return $p;
        });
    }

    public function update(int $id, UpdatePessoaRequest $r, ?int $currentUserId = null): Pessoa
    {
        $this->validator->validate($r);

        $pessoa = $this->getById($id);

        $documento = $this->normalizeDocumento($r->documento);

        if ($documento !== null && $this->repo->existsByCompanyAndDocumento((int) $pessoa->getCompany()->getId(), $documento, $id)) {
            throw new ValidationException(['documento' => ['Já existe uma pessoa com este documento na company informada.']]);
        }

        return $this->tx->run(function () use ($pessoa, $r, $documento, $currentUserId): Pessoa {
            $pessoa->update(
                $r->tipoPessoa,
                $r->nomeRazao,
                $r->nomeFantasia,
                $documento,
                $r->inscricaoEstadual,
                $r->emailPrincipal,
                $r->telefonePrincipal,
                $r->status,
                $currentUserId,
            );
            $this->repo->save($pessoa);
            $this->audit->log((int) $pessoa->getCompany()->getId(), 'pessoa', 'cadastro.pessoa.updated', ['pessoaId' => $pessoa->getId()]);

            return $pessoa;
        });
    }

    public function getById(int $id): Pessoa
    {
        $pessoa = $this->repo->findById($id);
        if ($pessoa === null) {
            throw new ResourceNotFoundException('Pessoa não encontrada.');
        }

        return $pessoa;
    }

    /**
     * @return list<Pessoa>
     */
    public function list(int $companyId, ?string $tipoPessoa = null, ?string $status = null): array
    {
        return $this->repo->listAll($companyId, $tipoPessoa, $status);
    }

    public function delete(int $id, ?int $currentUserId = null): void
    {
        $pessoa = $this->getById($id);

        $this->tx->run(function () use ($pessoa, $currentUserId): void {
            $this->repo->softDelete($pessoa);
            $this->audit->log((int) $pessoa->getCompany()->getId(), 'pessoa', 'cadastro.pessoa.deleted', ['pessoaId' => $pessoa->getId()]);
        });
    }

    private function normalizeDocumento(?string $documento): ?string
    {
        if ($documento === null || $documento === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $documento) ?? '';

        return $digits === '' ? null : $digits;
    }
}
