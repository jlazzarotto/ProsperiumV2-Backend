<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Repository;
use App\Cobranca\Domain\Entity\BorderoPagamento;
interface BorderoPagamentoRepositoryInterface { public function save(BorderoPagamento $bordero): void; }
