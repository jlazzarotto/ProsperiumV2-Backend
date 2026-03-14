<?php

declare(strict_types=1);

namespace App\Module\Empresa\Application\Service;

use App\Module\Empresa\Application\DTO\CreateEmpresaDTO;
use App\Module\Empresa\Application\DTO\UpdateEmpresaDTO;
use App\Module\Empresa\Domain\Entity\Empresa;
use App\Module\Empresa\Infrastructure\Repository\EmpresaRepository;

class EmpresaService
{
    public function __construct(private readonly EmpresaRepository $repository)
    {
    }

    /**
     * @return list<Empresa>
     */
    public function listar(): array
    {
        /** @var list<Empresa> $empresas */
        $empresas = $this->repository->findBy([], ['id' => 'ASC']);

        return $empresas;
    }

    public function buscar(int $id): ?Empresa
    {
        return $this->repository->find($id);
    }

    public function criar(CreateEmpresaDTO $dto): Empresa
    {
        $empresa = new Empresa();
        $empresa->setRazaoSocial($dto->razaoSocial);
        $empresa->setNomeFantasia($dto->nomeFantasia);
        $empresa->setCnpj($dto->cnpj);
        $empresa->atualizar();

        $this->repository->salvar($empresa);

        return $empresa;
    }

    public function atualizar(int $id, UpdateEmpresaDTO $dto): ?Empresa
    {
        $empresa = $this->repository->find($id);

        if (!$empresa) {
            return null;
        }

        if ($dto->razaoSocial !== null) {
            $empresa->setRazaoSocial($dto->razaoSocial);
        }

        if ($dto->nomeFantasia !== null) {
            $empresa->setNomeFantasia($dto->nomeFantasia);
        }

        if ($dto->cnpj !== null) {
            $empresa->setCnpj($dto->cnpj);
        }

        $empresa->atualizar();
        $this->repository->salvar($empresa);

        return $empresa;
    }

    public function inativar(int $id): bool
    {
        $empresa = $this->repository->find($id);

        if (!$empresa) {
            return false;
        }

        $empresa->inativar();
        $this->repository->salvar($empresa);

        return true;
    }
}
