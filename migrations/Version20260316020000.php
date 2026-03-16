<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260316020000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Oculta Permissões, Logs de Auditoria e Porto Sem Papel (PSP) do menu Administrador.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE modulos SET is_menu_entry = 0 WHERE codigo IN ('admin.permissoes', 'admin.logs_auditoria', 'admin.importacao_dados')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE modulos SET is_menu_entry = 1 WHERE codigo IN ('admin.permissoes', 'admin.logs_auditoria', 'admin.importacao_dados')");
    }
}
