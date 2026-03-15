<?php
declare(strict_types=1);
namespace App\Financeiro\Domain\Repository;
use App\Financeiro\Domain\Entity\TituloParcela;
interface TituloParcelaRepositoryInterface { public function save(TituloParcela $parcela): void; public function removeByTituloId(int $tituloId): void; public function findById(int $id): ?TituloParcela; /** @return list<TituloParcela> */ public function findByTituloId(int $tituloId): array; }
