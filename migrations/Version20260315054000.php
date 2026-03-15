<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315054000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renomeia o identificador técnico do módulo de cadastros.contas_caixa para cadastros.contas_financeiras.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE modulos SET codigo = 'cadastros.contas_financeiras' WHERE codigo = 'cadastros.contas_caixa'");
        $this->addSql("UPDATE permissoes SET codigo = 'cadastros.contas_financeiras.view' WHERE codigo = 'cadastros.contas_caixa.view'");
        $this->addSql("UPDATE permissoes SET codigo = 'cadastros.contas_financeiras.create_edit' WHERE codigo = 'cadastros.contas_caixa.create_edit'");
        $this->addSql("UPDATE permissoes SET codigo = 'cadastros.contas_financeiras.delete' WHERE codigo = 'cadastros.contas_caixa.delete'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE modulos SET codigo = 'cadastros.contas_caixa' WHERE codigo = 'cadastros.contas_financeiras'");
        $this->addSql("UPDATE permissoes SET codigo = 'cadastros.contas_caixa.view' WHERE codigo = 'cadastros.contas_financeiras.view'");
        $this->addSql("UPDATE permissoes SET codigo = 'cadastros.contas_caixa.create_edit' WHERE codigo = 'cadastros.contas_financeiras.create_edit'");
        $this->addSql("UPDATE permissoes SET codigo = 'cadastros.contas_caixa.delete' WHERE codigo = 'cadastros.contas_financeiras.delete'");
    }
}
