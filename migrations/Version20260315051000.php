<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315051000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Oculta Agências Bancárias do menu e renomeia Contas Caixa para Contas Financeiras.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE modulos SET is_menu_entry = 0 WHERE codigo = 'cadastros.agencias_bancarias'");
        $this->addSql("UPDATE modulos SET nome = 'Contas Financeiras', menu_label = 'Contas Financeiras' WHERE codigo = 'cadastros.contas_caixa'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE modulos SET is_menu_entry = 1 WHERE codigo = 'cadastros.agencias_bancarias'");
        $this->addSql("UPDATE modulos SET nome = 'Contas Caixa', menu_label = 'Contas Caixa' WHERE codigo = 'cadastros.contas_caixa'");
    }
}
