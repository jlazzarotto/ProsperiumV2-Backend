<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Repository;
use App\Bpo\Domain\Entity\NotificacaoSistema;
interface NotificacaoSistemaRepositoryInterface { public function save(NotificacaoSistema $notificacao): void; }
