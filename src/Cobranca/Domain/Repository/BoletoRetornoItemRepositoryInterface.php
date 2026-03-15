<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Repository;
use App\Cobranca\Domain\Entity\BoletoRetornoItem;
interface BoletoRetornoItemRepositoryInterface { public function save(BoletoRetornoItem $item): void; }
