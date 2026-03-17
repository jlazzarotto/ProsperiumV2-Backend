<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260317123952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE empresas CHANGE deleted_at deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE empresas RENAME INDEX idx_empresas_company TO IDX_70DD49A5979B1AD6');
        $this->addSql('DROP INDEX uk_tenant_instances_dbkey ON tenant_instances');
        $this->addSql('DROP INDEX idx_users_email_status ON users');
        $this->addSql('ALTER TABLE users CHANGE ultimo_login ultimo_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE locked_until locked_until DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE empresas CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE empresas RENAME INDEX idx_70dd49a5979b1ad6 TO idx_empresas_company');
        $this->addSql('CREATE UNIQUE INDEX uk_tenant_instances_dbkey ON tenant_instances (database_key)');
        $this->addSql('ALTER TABLE users CHANGE ultimo_login ultimo_login DATETIME DEFAULT NULL, CHANGE locked_until locked_until DATETIME DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_users_email_status ON users (email, status)');
    }
}
