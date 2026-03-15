<?php

declare(strict_types=1);

namespace App\Financeiro\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\Financeiro\Infrastructure\Persistence\Doctrine\DoctrineAnexoFinanceiroRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineAnexoFinanceiroRepository::class)]
#[ORM\Table(name: 'anexos_financeiros')]
class AnexoFinanceiro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Company $company;
    #[ORM\ManyToOne(targetEntity: Titulo::class)]
    #[ORM\JoinColumn(name: 'titulo_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Titulo $titulo;
    #[ORM\Column(name: 'file_name', length: 255)]
    private string $fileName;
    #[ORM\Column(name: 'file_path', length: 255)]
    private string $filePath;
    #[ORM\Column(name: 'mime_type', length: 100, nullable: true)]
    private ?string $mimeType;
    #[ORM\Column(name: 'uploaded_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $uploadedAt;
    public function __construct(Company $company, Titulo $titulo, string $fileName, string $filePath, ?string $mimeType)
    { $this->company=$company; $this->titulo=$titulo; $this->fileName=trim($fileName); $this->filePath=trim($filePath); $this->mimeType=$mimeType !== null ? trim($mimeType) : null; $this->uploadedAt=new \DateTimeImmutable(); }
}
