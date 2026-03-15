<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Empresa;
use App\Company\Domain\Entity\UnidadeNegocio;
use App\Identity\Infrastructure\Persistence\Doctrine\DoctrineUserAlcadaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineUserAlcadaRepository::class)]
#[ORM\Table(name: 'user_alcadas')]
class UserAlcada
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Company $company;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Empresa $empresa;

    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)]
    #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?UnidadeNegocio $unidade;

    #[ORM\Column(name: 'tipo_operacao', length: 100)]
    private string $tipoOperacao;

    #[ORM\Column(name: 'valor_limite', type: 'decimal', precision: 18, scale: 2, nullable: true)]
    private ?string $valorLimite;

    #[ORM\Column(length: 30, options: ['default' => 'active'])]
    private string $status;
}
