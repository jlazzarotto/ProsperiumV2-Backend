<?php

declare(strict_types=1);

namespace App\Company\Application\Service;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Company\Domain\Repository\TenantInstanceRepositoryInterface;
use App\Shared\Domain\Contract\TenantDatabaseRegistryInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

final class TenantCatalogProjector
{
    public function __construct(
        private readonly TenantDatabaseRegistryInterface $tenantDatabaseRegistry,
        private readonly TenantInstanceRepositoryInterface $tenantInstanceRepository
    ) {
    }

    public function syncCompany(Company $company, string $databaseKey): void
    {
        $connection = $this->openTenantConnection($databaseKey);

        try {
            $connection->executeStatement(
                'INSERT INTO companies (id, nome, status, created_at, updated_at)
                 VALUES (:id, :nome, :status, :createdAt, :updatedAt)
                 ON DUPLICATE KEY UPDATE nome = VALUES(nome), status = VALUES(status), updated_at = VALUES(updated_at)',
                [
                    'id' => $company->getId(),
                    'nome' => $company->getNome(),
                    'status' => $company->getStatus(),
                    'createdAt' => $company->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $company->getUpdatedAt()->format('Y-m-d H:i:s'),
                ]
            );
        } finally {
            $connection->close();
        }
    }

    public function syncEmpresa(Empresa $empresa): void
    {
        $companyId = (int) $empresa->getCompany()->getId();
        $databaseKey = $this->resolveDatabaseKeyByCompanyId($companyId);
        $connection = $this->openTenantConnection($databaseKey);

        try {
            $connection->executeStatement(
                'INSERT INTO empresas (
                    id, company_id, razao_social, nome_fantasia, apelido, abreviatura,
                    cnpj, inscricao_estadual, inscricao_municipal,
                    cep, estado, cidade, logradouro, numero, complemento, bairro,
                    status, created_at, updated_at, deleted_at
                 )
                 VALUES (
                    :id, :companyId, :razaoSocial, :nomeFantasia, :apelido, :abreviatura,
                    :cnpj, :inscricaoEstadual, :inscricaoMunicipal,
                    :cep, :estado, :cidade, :logradouro, :numero, :complemento, :bairro,
                    :status, :createdAt, :updatedAt, :deletedAt
                 )
                 ON DUPLICATE KEY UPDATE
                    company_id = VALUES(company_id),
                    razao_social = VALUES(razao_social),
                    nome_fantasia = VALUES(nome_fantasia),
                    apelido = VALUES(apelido),
                    abreviatura = VALUES(abreviatura),
                    cnpj = VALUES(cnpj),
                    inscricao_estadual = VALUES(inscricao_estadual),
                    inscricao_municipal = VALUES(inscricao_municipal),
                    cep = VALUES(cep),
                    estado = VALUES(estado),
                    cidade = VALUES(cidade),
                    logradouro = VALUES(logradouro),
                    numero = VALUES(numero),
                    complemento = VALUES(complemento),
                    bairro = VALUES(bairro),
                    status = VALUES(status),
                    updated_at = VALUES(updated_at),
                    deleted_at = VALUES(deleted_at)',
                [
                    'id' => $empresa->getId(),
                    'companyId' => $companyId,
                    'razaoSocial' => $empresa->getRazaoSocial(),
                    'nomeFantasia' => $empresa->getNomeFantasia(),
                    'apelido' => $empresa->getApelido(),
                    'abreviatura' => $empresa->getAbreviatura(),
                    'cnpj' => $empresa->getCnpj(),
                    'inscricaoEstadual' => $empresa->getInscricaoEstadual(),
                    'inscricaoMunicipal' => $empresa->getInscricaoMunicipal(),
                    'cep' => $empresa->getCep(),
                    'estado' => $empresa->getEstado(),
                    'cidade' => $empresa->getCidade(),
                    'logradouro' => $empresa->getLogradouro(),
                    'numero' => $empresa->getNumero(),
                    'complemento' => $empresa->getComplemento(),
                    'bairro' => $empresa->getBairro(),
                    'status' => $empresa->getStatus(),
                    'createdAt' => $empresa->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $empresa->getUpdatedAt()->format('Y-m-d H:i:s'),
                    'deletedAt' => $empresa->getDeletedAt()?->format('Y-m-d H:i:s'),
                ]
            );
        } finally {
            $connection->close();
        }
    }

    public function syncUnidadeNegocio(UnidadeNegocio $unidadeNegocio): void
    {
        $companyId = (int) $unidadeNegocio->getCompany()->getId();
        $databaseKey = $this->resolveDatabaseKeyByCompanyId($companyId);
        $connection = $this->openTenantConnection($databaseKey);

        try {
            $connection->executeStatement(
                'INSERT INTO unidades_negocio (id, company_id, nome, abreviatura, status, created_at, updated_at)
                 VALUES (:id, :companyId, :nome, :abreviatura, :status, :createdAt, :updatedAt)
                 ON DUPLICATE KEY UPDATE
                    company_id = VALUES(company_id),
                    nome = VALUES(nome),
                    abreviatura = VALUES(abreviatura),
                    status = VALUES(status),
                    updated_at = VALUES(updated_at)',
                [
                    'id' => $unidadeNegocio->getId(),
                    'companyId' => $companyId,
                    'nome' => $unidadeNegocio->getNome(),
                    'abreviatura' => $unidadeNegocio->getAbreviatura(),
                    'status' => $unidadeNegocio->getStatus(),
                    'createdAt' => $unidadeNegocio->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $unidadeNegocio->getUpdatedAt()->format('Y-m-d H:i:s'),
                ]
            );
        } finally {
            $connection->close();
        }
    }

    private function resolveDatabaseKeyByCompanyId(int $companyId): string
    {
        $tenantInstance = $this->tenantInstanceRepository->findByCompanyId($companyId);

        if ($tenantInstance === null) {
            throw new ResourceNotFoundException('TenantInstance não encontrada para sincronização tenant.');
        }

        return $tenantInstance->getDatabaseKey();
    }

    private function openTenantConnection(string $databaseKey): Connection
    {
        $databaseUrl = $this->tenantDatabaseRegistry->findDatabaseUrl($databaseKey);

        if ($databaseUrl === null) {
            throw new ValidationException([
                'databaseKey' => ['databaseKey sem database_url configurado para projeção tenant.'],
            ]);
        }

        return DriverManager::getConnection(['url' => $databaseUrl]);
    }
}
