<?php
declare(strict_types=1);
namespace App\Contabil\Domain\Entity;
use App\Cadastro\Domain\Entity\CategoriaFinanceira;
use App\Contabil\Infrastructure\Persistence\Doctrine\DoctrineDreMapeamentoCategoriaRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineDreMapeamentoCategoriaRepository::class)]
#[ORM\Table(name: 'dre_mapeamento_categorias')]
class DreMapeamentoCategoria
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\Column(name: 'company_id', type: 'bigint', options: ['unsigned' => true])] private int $companyId;
    #[ORM\ManyToOne(targetEntity: DreGrupo::class)] #[ORM\JoinColumn(name: 'dre_grupo_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private DreGrupo $dreGrupo;
    #[ORM\ManyToOne(targetEntity: CategoriaFinanceira::class)] #[ORM\JoinColumn(name: 'categoria_financeira_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private CategoriaFinanceira $categoriaFinanceira;
    public function __construct(int $companyId, DreGrupo $dreGrupo, CategoriaFinanceira $categoriaFinanceira) { $this->companyId = $companyId; $this->dreGrupo = $dreGrupo; $this->categoriaFinanceira = $categoriaFinanceira; }
    public function getId(): ?int { return $this->id; } public function getCompanyId(): int { return $this->companyId; } public function getDreGrupo(): DreGrupo { return $this->dreGrupo; } public function getCategoriaFinanceira(): CategoriaFinanceira { return $this->categoriaFinanceira; }
}
