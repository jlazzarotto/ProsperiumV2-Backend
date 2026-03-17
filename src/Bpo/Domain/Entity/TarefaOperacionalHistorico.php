<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Entity;
use App\Bpo\Infrastructure\Persistence\Doctrine\DoctrineTarefaOperacionalHistoricoRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineTarefaOperacionalHistoricoRepository::class)]
#[ORM\Table(name: 'tarefas_operacionais_bpo_historico')]
class TarefaOperacionalHistorico
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: TarefaOperacionalBpo::class)] #[ORM\JoinColumn(name: 'tarefa_operacional_bpo_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private TarefaOperacionalBpo $tarefa;
    #[ORM\Column(name: 'user_id', type: 'bigint', nullable: true, options: ['unsigned' => true])] private ?int $userId;
    #[ORM\Column(length: 100)] private string $acao;
    #[ORM\Column(type: 'text', nullable: true)] private ?string $observacao;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')] private \DateTimeImmutable $createdAt;
    public function __construct(TarefaOperacionalBpo $tarefa, ?int $userId, string $acao, ?string $observacao = null) { $this->tarefa = $tarefa; $this->userId = $userId; $this->acao = trim($acao); $this->observacao = $observacao !== null ? trim($observacao) : null; $this->createdAt = new \DateTimeImmutable(); }
}
