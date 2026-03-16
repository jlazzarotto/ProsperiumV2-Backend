<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260316040000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adiciona coluna classificacao (C/F/U) na tabela pessoas.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE pessoas ADD COLUMN classificacao VARCHAR(3) DEFAULT NULL AFTER tipo_pessoa");
        $this->addSql("CREATE INDEX idx_pessoas_classificacao ON pessoas (company_id, classificacao)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP INDEX idx_pessoas_classificacao ON pessoas");
        $this->addSql("ALTER TABLE pessoas DROP COLUMN classificacao");
    }
}
