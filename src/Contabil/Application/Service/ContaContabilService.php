<?php
declare(strict_types=1);
namespace App\Contabil\Application\Service;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Contabil\Application\DTO\CreateContaContabilRequest;
use App\Contabil\Domain\Entity\ContaContabil;
use App\Contabil\Domain\Repository\ContaContabilRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class ContaContabilService
{
    public function __construct(private readonly ContaContabilRepositoryInterface $repo, private readonly CompanyRepositoryInterface $companyRepo, private readonly RequestValidator $validator, private readonly AuditoriaLogger $audit) {}
    public function create(CreateContaContabilRequest $r): ContaContabil
    {
        $this->validator->validate($r);
        $company=$this->companyRepo->findById((int)$r->companyId); if($company===null){ throw new ResourceNotFoundException('Company não encontrada.'); }
        $parent=$r->parentId!==null?$this->repo->findById($r->parentId):null;
        if($r->parentId!==null && ($parent===null || $parent->getCompany()->getId()!==$company->getId())){ throw new ValidationException(['parentId'=>['Conta contábil pai inválida para a company informada.']]); }
        $conta=new ContaContabil($company,$parent,$r->codigo,$r->nome,$r->tipo,$r->status);
        $this->repo->save($conta);
        $this->audit->log((int)$company->getId(),'conta_contabil','contabil.conta_contabil.criada',['contaContabilId'=>$conta->getId()]);
        return $conta;
    }
    /** @return list<ContaContabil> */ public function list(int $companyId, ?string $tipo = null, ?string $status = null): array { return $this->repo->listAll($companyId,$tipo,$status); }
}
