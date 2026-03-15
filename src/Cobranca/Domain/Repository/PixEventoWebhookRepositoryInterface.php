<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Repository;
use App\Cobranca\Domain\Entity\PixEventoWebhook;
interface PixEventoWebhookRepositoryInterface { public function save(PixEventoWebhook $evento): void; }
