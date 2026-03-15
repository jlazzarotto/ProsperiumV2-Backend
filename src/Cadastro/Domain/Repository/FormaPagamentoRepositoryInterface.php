<?php
declare(strict_types=1);
namespace App\Cadastro\Domain\Repository;
use App\Cadastro\Domain\Entity\FormaPagamento;
interface FormaPagamentoRepositoryInterface { public function save(FormaPagamento $formaPagamento): void; public function findById(int $id): ?FormaPagamento; /** @return list<FormaPagamento> */ public function listAll(int $companyId, ?string $status = null): array; }
