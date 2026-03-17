<?php

declare(strict_types=1);

namespace App\Cadastro\Domain\Entity;

use App\Cadastro\Infrastructure\Persistence\Doctrine\DoctrinePessoaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrinePessoaRepository::class)]
#[ORM\Table(name: 'pessoas')]
#[ORM\UniqueConstraint(name: 'uk_pessoas_company_documento', columns: ['company_id', 'documento'])]
#[ORM\UniqueConstraint(name: 'uk_pessoas_company_email', columns: ['company_id', 'email_principal'])]
#[ORM\Index(name: 'idx_pessoas_company', columns: ['company_id', 'status'])]
#[ORM\Index(name: 'idx_pessoas_company_nome', columns: ['company_id', 'nome_razao'])]
class Pessoa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])]
    private int $companyId;

    #[ORM\Column(name: 'tipo_pessoa', type: 'string', length: 2)]
    private string $tipoPessoa;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $classificacao;

    #[ORM\Column(name: 'nome_razao', length: 180)]
    private string $nomeRazao;

    #[ORM\Column(name: 'nome_fantasia', length: 180, nullable: true)]
    private ?string $nomeFantasia;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $documento;

    #[ORM\Column(name: 'inscricao_estadual', length: 30, nullable: true)]
    private ?string $inscricaoEstadual;

    #[ORM\Column(name: 'email_principal', length: 160, nullable: true)]
    private ?string $emailPrincipal;

    #[ORM\Column(name: 'telefone_principal', length: 30, nullable: true)]
    private ?string $telefonePrincipal;

    #[ORM\Column(length: 20, options: ['default' => 'active'])]
    private string $status;

    #[ORM\Column(name: 'created_by', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $createdBy;

    #[ORM\Column(name: 'updated_by', type: 'bigint', nullable: true, options: ['unsigned' => true])]
    private ?int $updatedBy;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct(
        int $companyId,
        string $tipoPessoa,
        string $nomeRazao,
        ?string $nomeFantasia = null,
        ?string $documento = null,
        ?string $inscricaoEstadual = null,
        ?string $emailPrincipal = null,
        ?string $telefonePrincipal = null,
        string $status = 'active',
        ?int $createdBy = null,
        ?string $classificacao = null,
    ) {
        $now = new \DateTimeImmutable();
        $this->companyId = $companyId;
        $this->tipoPessoa = strtoupper(trim($tipoPessoa));
        $this->classificacao = self::normalizeClassificacao($classificacao);
        $this->nomeRazao = trim($nomeRazao);
        $this->nomeFantasia = $nomeFantasia !== null ? trim($nomeFantasia) : null;
        $this->documento = $documento !== null ? preg_replace('/\D+/', '', $documento) ?: null : null;
        $this->inscricaoEstadual = $inscricaoEstadual !== null ? trim($inscricaoEstadual) : null;
        $this->emailPrincipal = $emailPrincipal !== null ? trim($emailPrincipal) : null;
        $this->telefonePrincipal = $telefonePrincipal !== null ? trim($telefonePrincipal) : null;
        $this->status = $status;
        $this->createdBy = $createdBy;
        $this->updatedBy = $createdBy;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getTipoPessoa(): string { return $this->tipoPessoa; }
    public function getClassificacao(): ?string { return $this->classificacao; }
    public function getNomeRazao(): string { return $this->nomeRazao; }
    public function getNomeFantasia(): ?string { return $this->nomeFantasia; }
    public function getDocumento(): ?string { return $this->documento; }
    public function getInscricaoEstadual(): ?string { return $this->inscricaoEstadual; }
    public function getEmailPrincipal(): ?string { return $this->emailPrincipal; }
    public function getTelefonePrincipal(): ?string { return $this->telefonePrincipal; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedBy(): ?int { return $this->createdBy; }
    public function getUpdatedBy(): ?int { return $this->updatedBy; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function getDeletedAt(): ?\DateTimeImmutable { return $this->deletedAt; }

    public function update(
        string $tipoPessoa,
        string $nomeRazao,
        ?string $nomeFantasia,
        ?string $documento,
        ?string $inscricaoEstadual,
        ?string $emailPrincipal,
        ?string $telefonePrincipal,
        string $status,
        ?int $updatedBy = null,
        ?string $classificacao = null,
    ): void {
        $this->tipoPessoa = strtoupper(trim($tipoPessoa));
        $this->classificacao = self::normalizeClassificacao($classificacao);
        $this->nomeRazao = trim($nomeRazao);
        $this->nomeFantasia = $nomeFantasia !== null ? trim($nomeFantasia) : null;
        $this->documento = $documento !== null ? preg_replace('/\D+/', '', $documento) ?: null : null;
        $this->inscricaoEstadual = $inscricaoEstadual !== null ? trim($inscricaoEstadual) : null;
        $this->emailPrincipal = $emailPrincipal !== null ? trim($emailPrincipal) : null;
        $this->telefonePrincipal = $telefonePrincipal !== null ? trim($telefonePrincipal) : null;
        $this->status = $status;
        $this->updatedBy = $updatedBy;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function softDelete(?int $updatedBy = null): void
    {
        $this->deletedAt = new \DateTimeImmutable();
        $this->status = 'inactive';
        $this->updatedBy = $updatedBy;
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Normaliza classificacao: mantém apenas C, F, U na ordem canônica.
     */
    private static function normalizeClassificacao(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $upper = strtoupper($value);
        $result = '';
        foreach (['C', 'F', 'U'] as $char) {
            if (str_contains($upper, $char)) {
                $result .= $char;
            }
        }

        return $result === '' ? null : $result;
    }

    /** @deprecated Use getNomeRazao() */
    public function getNome(): string { return $this->nomeRazao; }
}
