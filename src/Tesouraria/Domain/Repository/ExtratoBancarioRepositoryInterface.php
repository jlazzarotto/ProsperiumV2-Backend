<?php
declare(strict_types=1);
namespace App\Tesouraria\Domain\Repository;
use App\Tesouraria\Domain\Entity\ExtratoBancario;
interface ExtratoBancarioRepositoryInterface { public function save(ExtratoBancario $extrato): void; public function findById(int $id): ?ExtratoBancario; /** @return list<ExtratoBancario> */ public function listAll(int $companyId, ?int $empresaId = null, ?int $unidadeId = null, ?int $contaFinanceiraId = null, ?string $status = null): array; }
