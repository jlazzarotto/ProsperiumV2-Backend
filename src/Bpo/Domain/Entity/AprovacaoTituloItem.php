<?php
declare(strict_types=1);
namespace App\Bpo\Domain\Entity;
use App\Bpo\Infrastructure\Persistence\Doctrine\DoctrineAprovacaoTituloItemRepository;
use App\Identity\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineAprovacaoTituloItemRepository::class)]
#[ORM\Table(name: 'aprovacoes_titulos_itens')]
class AprovacaoTituloItem
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: AprovacaoTitulo::class)] #[ORM\JoinColumn(name: 'aprovacao_titulo_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private AprovacaoTitulo $aprovacao;
    #[ORM\ManyToOne(targetEntity: User::class)] #[ORM\JoinColumn(name: 'aprovador_user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private User $aprovador;
    #[ORM\Column] private int $ordem;
    #[ORM\Column(name: 'limite_alcada', type: 'decimal', precision: 18, scale: 2, nullable: true)] private ?string $limiteAlcada;
    #[ORM\Column(length: 30, options: ['default' => 'pendente'])] private string $status;
    #[ORM\Column(type: 'text', nullable: true)] private ?string $observacao = null;
    #[ORM\Column(name: 'decidido_em', type: 'datetime_immutable', nullable: true)] private ?\DateTimeImmutable $decididoEm = null;
    public function __construct(AprovacaoTitulo $aprovacao, User $aprovador, int $ordem, ?string $limiteAlcada = null) { $this->aprovacao = $aprovacao; $this->aprovador = $aprovador; $this->ordem = $ordem; $this->limiteAlcada = $limiteAlcada; $this->status = 'pendente'; }
    public function getId(): ?int { return $this->id; } public function getAprovacao(): AprovacaoTitulo { return $this->aprovacao; } public function getAprovador(): User { return $this->aprovador; } public function getStatus(): string { return $this->status; }
    public function aprovar(?string $observacao = null): void { $this->status = 'aprovado'; $this->observacao = $observacao; $this->decididoEm = new \DateTimeImmutable(); }
}
