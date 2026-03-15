<?php
declare(strict_types=1);
namespace App\Cobranca\Domain\Entity;
use App\Cobranca\Infrastructure\Persistence\Doctrine\DoctrineBorderoPagamentoItemRepository;
use App\Financeiro\Domain\Entity\TituloParcela;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineBorderoPagamentoItemRepository::class)]
#[ORM\Table(name: 'borderos_pagamento_itens')]
class BorderoPagamentoItem
{
    #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column(type: 'bigint', options: ['unsigned' => true])] private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: BorderoPagamento::class)] #[ORM\JoinColumn(name: 'bordero_pagamento_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private BorderoPagamento $bordero;
    #[ORM\ManyToOne(targetEntity: TituloParcela::class)] #[ORM\JoinColumn(name: 'titulo_parcela_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] private TituloParcela $parcela;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)] private string $valor;
    #[ORM\Column(length: 30, options: ['default' => 'pendente'])] private string $status;
    public function __construct(BorderoPagamento $bordero, TituloParcela $parcela, string $valor, string $status = 'pendente') { $this->bordero = $bordero; $this->parcela = $parcela; $this->valor = $valor; $this->status = $status; }
}
