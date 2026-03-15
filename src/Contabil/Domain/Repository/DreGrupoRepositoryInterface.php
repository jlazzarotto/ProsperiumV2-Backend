<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Repository;
use App\Contabil\Domain\Entity\DreGrupo;
interface DreGrupoRepositoryInterface { public function save(DreGrupo $grupo): void; /** @return list<DreGrupo> */ public function listAll(int $companyId, ?string $status = null): array; }
