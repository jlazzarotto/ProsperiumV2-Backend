<?php

declare(strict_types=1);

namespace App\Company\Application\DTO;

use App\Company\Domain\Entity\Tenant\Empresa;

final class EmpresaResponse
{
    public static function fromEntity(Empresa $empresa): array
    {
        return [
            'id' => $empresa->getId(),
            'companyId' => $empresa->getCompanyId(),
            'razaoSocial' => $empresa->getRazaoSocial(),
            'nomeFantasia' => $empresa->getNomeFantasia(),
            'apelido' => $empresa->getApelido(),
            'abreviatura' => $empresa->getAbreviatura(),
            'cnpj' => $empresa->getCnpj(),
            'cpfCnpj' => $empresa->getCnpj(),
            'inscricaoEstadual' => $empresa->getInscricaoEstadual(),
            'inscricaoMunicipal' => $empresa->getInscricaoMunicipal(),
            'cep' => $empresa->getCep(),
            'estado' => $empresa->getEstado(),
            'cidade' => $empresa->getCidade(),
            'logradouro' => $empresa->getLogradouro(),
            'numero' => $empresa->getNumero(),
            'complemento' => $empresa->getComplemento(),
            'bairro' => $empresa->getBairro(),
            'endereco' => [
                'cep' => $empresa->getCep(),
                'estado' => $empresa->getEstado(),
                'cidade' => $empresa->getCidade(),
                'logradouro' => $empresa->getLogradouro(),
                'numero' => $empresa->getNumero(),
                'complemento' => $empresa->getComplemento(),
                'bairro' => $empresa->getBairro(),
            ],
            'status' => $empresa->getStatus(),
        ];
    }
}
