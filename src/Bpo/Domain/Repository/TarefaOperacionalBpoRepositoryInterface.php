<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Repository;
use App\Bpo\Domain\Entity\TarefaOperacionalBpo;
interface TarefaOperacionalBpoRepositoryInterface { public function save(TarefaOperacionalBpo $tarefa): void; public function findById(int $id): ?TarefaOperacionalBpo; /** @return list<TarefaOperacionalBpo> */ public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?string $status = null): array; }
