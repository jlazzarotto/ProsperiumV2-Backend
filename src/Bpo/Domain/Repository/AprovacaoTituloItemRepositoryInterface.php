<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Repository;
use App\Bpo\Domain\Entity\AprovacaoTituloItem;
interface AprovacaoTituloItemRepositoryInterface { public function save(AprovacaoTituloItem $item): void; }
