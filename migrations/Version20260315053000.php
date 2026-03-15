<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315053000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Atualiza rota do módulo de contas financeiras para /cadastros/contas-financeiras.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE modulos SET route_path = '/cadastros/contas-financeiras' WHERE codigo = 'cadastros.contas_caixa'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE modulos SET route_path = '/cadastros/contas-caixa' WHERE codigo = 'cadastros.contas_caixa'");
    }
}
