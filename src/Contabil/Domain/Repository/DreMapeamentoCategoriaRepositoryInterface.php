<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Repository;
use App\Contabil\Domain\Entity\DreMapeamentoCategoria;
interface DreMapeamentoCategoriaRepositoryInterface { public function save(DreMapeamentoCategoria $mapeamento): void; /** @return list<DreMapeamentoCategoria> */ public function findByCompanyId(int $companyId): array; }
