<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Repository;
use App\Bpo\Domain\Entity\TarefaOperacionalHistorico;
interface TarefaOperacionalHistoricoRepositoryInterface { public function save(TarefaOperacionalHistorico $historico): void; }
