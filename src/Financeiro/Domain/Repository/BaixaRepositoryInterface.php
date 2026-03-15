<?php
declare(strict_types=1);
namespace App\Financeiro\Domain\Repository;
use App\Financeiro\Domain\Entity\Baixa;
interface BaixaRepositoryInterface { public function save(Baixa $baixa): void; public function findById(int $id): ?Baixa; /** @return list<Baixa> */ public function findByParcelaId(int $parcelaId): array; }
