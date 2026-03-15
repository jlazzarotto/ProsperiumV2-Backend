<?php
declare(strict_types=1);
namespace App\Tesouraria\Domain\Repository;
use App\Tesouraria\Domain\Entity\IntegracaoBancaria;
interface IntegracaoBancariaRepositoryInterface { public function save(IntegracaoBancaria $integracao): void; }
