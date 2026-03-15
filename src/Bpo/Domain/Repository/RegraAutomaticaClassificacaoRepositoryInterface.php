<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Repository;
use App\Bpo\Domain\Entity\RegraAutomaticaClassificacao;
interface RegraAutomaticaClassificacaoRepositoryInterface { public function save(RegraAutomaticaClassificacao $regra): void; /** @return list<RegraAutomaticaClassificacao> */ public function findActiveMatches(int $companyId, ?int $empresaId, ?int $unidadeId, string $texto): array; }
