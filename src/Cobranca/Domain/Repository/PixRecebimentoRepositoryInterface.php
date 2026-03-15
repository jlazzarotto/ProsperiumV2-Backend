<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Repository;
use App\Cobranca\Domain\Entity\PixRecebimento;
interface PixRecebimentoRepositoryInterface { public function save(PixRecebimento $recebimento): void; public function findByEndToEndId(string $endToEndId): ?PixRecebimento; }
