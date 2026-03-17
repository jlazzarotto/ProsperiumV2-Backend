<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

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

    #[ORM\Column(name: 'company_id', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $companyId = null;

    #[ORM\Column(name: 'empresa_id', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $empresaId = null;

    #[ORM\Column(name: 'unidade_id', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $unidadeId = null;

    #[ORM\Column(name: 'user_id', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $userId = null;

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
