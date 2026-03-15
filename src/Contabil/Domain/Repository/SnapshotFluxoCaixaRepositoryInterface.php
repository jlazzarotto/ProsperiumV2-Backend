<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Repository;
use App\Contabil\Domain\Entity\SnapshotFluxoCaixa;
interface SnapshotFluxoCaixaRepositoryInterface { public function save(SnapshotFluxoCaixa $snapshot): void; public function findByContextAndDate(int $companyId, int $empresaId, int $unidadeId, \DateTimeImmutable $data): ?SnapshotFluxoCaixa; }
