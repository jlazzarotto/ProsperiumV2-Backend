<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Identity\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'auditoria_logs')]
#[ORM\Index(name: 'idx_auditoria_logs_lookup', columns: ['company_id', 'recurso', 'acao', 'created_at'])]
#[ORM\Index(name: 'idx_auditoria_logs_empresa', columns: ['empresa_id'])]
#[ORM\Index(name: 'idx_auditoria_logs_unidade', columns: ['unidade_id'])]
#[ORM\Index(name: 'idx_auditoria_logs_user', columns: ['user_id'])]
class AuditoriaLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Company $company = null;

    #[ORM\ManyToOne(targetEntity: Empresa::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Empresa $empresa = null;

    #[ORM\ManyToOne(targetEntity: UnidadeNegocio::class)]
    #[ORM\JoinColumn(name: 'unidade_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?UnidadeNegocio $unidade = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(length: 100)]
    private string $recurso;

    #[ORM\Column(length: 120)]
    private string $acao;

    #[ORM\Column(name: 'payload_json', type: 'json', nullable: true)]
    private ?array $payloadJson = null;

    #[ORM\Column(name: 'request_id', length: 64, nullable: true)]
    private ?string $requestId = null;

    #[ORM\Column(name: 'request_path', length: 255, nullable: true)]
    private ?string $requestPath = null;

    #[ORM\Column(name: 'request_method', length: 10, nullable: true)]
    private ?string $requestMethod = null;

    #[ORM\Column(name: 'ip_address', length: 64, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
}
