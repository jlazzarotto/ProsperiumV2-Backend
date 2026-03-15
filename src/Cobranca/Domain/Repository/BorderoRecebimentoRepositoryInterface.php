<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Repository;
use App\Cobranca\Domain\Entity\BorderoRecebimento;
interface BorderoRecebimentoRepositoryInterface { public function save(BorderoRecebimento $bordero): void; }
