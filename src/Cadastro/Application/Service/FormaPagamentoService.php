<?php
declare(strict_types=1);
namespace App\Cadastro\Application\Service;
use App\Cadastro\Application\DTO\CreateFormaPagamentoRequest;
use App\Cadastro\Domain\Entity\FormaPagamento;
use App\Cadastro\Domain\Repository\FormaPagamentoRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class FormaPagamentoService {
    public function __construct(private readonly FormaPagamentoRepositoryInterface $repo, private readonly CompanyRepositoryInterface $companyRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit) {}
    public function create(CreateFormaPagamentoRequest $r): FormaPagamento { $this->validator->validate($r); $company=$this->companyRepo->findById((int)$r->companyId); if(!$company){throw new ResourceNotFoundException('Company não encontrada.');} return $this->tx->run(function() use($r,$company): FormaPagamento { $f=new FormaPagamento($company,$r->codigo,$r->nome,$r->tipo,$r->status); $this->repo->save($f); $this->audit->log((int)$company->getId(),'forma_pagamento','cadastro.forma_pagamento.created',['formaPagamentoId'=>$f->getId()]); return $f;}); }
    public function list(int $companyId, ?string $status=null): array { return $this->repo->listAll($companyId,$status); }
}
