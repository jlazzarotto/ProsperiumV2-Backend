<?php
declare(strict_types=1);
namespace App\Cadastro\Domain\Repository;
use App\Cadastro\Domain\Entity\Pessoa;
interface PessoaRepositoryInterface { public function save(Pessoa $pessoa): void; public function findById(int $id): ?Pessoa; /** @return list<Pessoa> */ public function listAll(int $companyId, ?int $empresaId = null, ?string $status = null): array; }
