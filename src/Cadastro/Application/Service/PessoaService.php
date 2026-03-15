<?php
declare(strict_types=1);
namespace App\Cadastro\Application\Service;
use App\Cadastro\Application\DTO\CreatePessoaRequest;
use App\Cadastro\Domain\Entity\Pessoa;
use App\Cadastro\Domain\Repository\PessoaRepositoryInterface;
use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
final class PessoaService {
    public function __construct(private readonly PessoaRepositoryInterface $repo, private readonly CompanyRepositoryInterface $companyRepo, private readonly EmpresaRepositoryInterface $empresaRepo, private readonly RequestValidator $validator, private readonly TransactionRunnerInterface $tx, private readonly AuditoriaLogger $audit) {}
    public function create(CreatePessoaRequest $r): Pessoa { $this->validator->validate($r); $company=$this->companyRepo->findById((int)$r->companyId); if(!$company){throw new ResourceNotFoundException('Company não encontrada.');} $empresa=null; if($r->empresaId!==null){$empresa=$this->empresaRepo->findById($r->empresaId); if($empresa===null||$empresa->getCompany()->getId()!==$company->getId()){throw new ValidationException(['empresaId'=>['Empresa inválida para a company informada.']]);}} return $this->tx->run(function() use($r,$company,$empresa): Pessoa { $p=new Pessoa($company,$empresa,$r->nome,$r->documento,$r->classificacao,$r->status); $this->repo->save($p); $this->audit->log((int)$company->getId(),'pessoa','cadastro.pessoa.created',['pessoaId'=>$p->getId()]); return $p;}); }
    public function list(int $companyId, ?int $empresaId=null, ?string $status=null): array { return $this->repo->listAll($companyId,$empresaId,$status); }
}
