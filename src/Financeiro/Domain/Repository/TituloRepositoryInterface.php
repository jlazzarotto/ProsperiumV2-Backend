<?php
declare(strict_types=1);
namespace App\Financeiro\Domain\Repository;
use App\Financeiro\Domain\Entity\Titulo;
interface TituloRepositoryInterface { public function save(Titulo $titulo): void; public function findById(int $id): ?Titulo; /** @return list<Titulo> */ public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $tipo = null, ?string $status = null): array; }
