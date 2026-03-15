<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Repository;
use App\Bpo\Domain\Entity\ComentarioTitulo;
interface ComentarioTituloRepositoryInterface { public function save(ComentarioTitulo $comentario): void; }
