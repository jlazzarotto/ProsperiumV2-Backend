<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Repository;
use App\Bpo\Domain\Entity\AprovacaoTitulo;
interface AprovacaoTituloRepositoryInterface { public function save(AprovacaoTitulo $aprovacao): void; public function findById(int $id): ?AprovacaoTitulo; }
