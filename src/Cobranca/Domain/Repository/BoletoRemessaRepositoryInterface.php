<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Repository;
use App\Cobranca\Domain\Entity\BoletoRemessa;
interface BoletoRemessaRepositoryInterface { public function save(BoletoRemessa $remessa): void; public function findById(int $id): ?BoletoRemessa; }
