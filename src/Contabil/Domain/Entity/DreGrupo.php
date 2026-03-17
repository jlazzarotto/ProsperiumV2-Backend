<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Entity;
use App\Contabil\Infrastructure\Persistence\Doctrine\DoctrineDreGrupoRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineDreGrupoRepository::class)]
#[ORM\Table(name: 'dre_grupos')]
#[ORM\Index(name: 'idx_dre_grupos_lookup', columns: ['company_id', 'ordem', 'status'])]
class DreGrupo
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\Column(length: 50)] private string $codigo;
    #[ORM\Column(length: 255)] private string $nome;
    #[ORM\Column] private int $ordem;
    #[ORM\Column(length: 30)] private string $tipoDemonstracao;
    #[ORM\Column(length: 30, options: ['default' => 'active'])] private string $status;
    public function __construct(int $companyId, string $codigo, string $nome, int $ordem, string $tipoDemonstracao = 'resultado', string $status = 'active') { $this->companyId = $companyId; $this->codigo = trim($codigo); $this->nome = trim($nome); $this->ordem = $ordem; $this->tipoDemonstracao = $tipoDemonstracao; $this->status = $status; }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getCodigo(): string { return $this->codigo; } public function getNome(): string { return $this->nome; } public function getOrdem(): int { return $this->ordem; } public function getTipoDemonstracao(): string { return $this->tipoDemonstracao; } public function getStatus(): string { return $this->status; }
}
