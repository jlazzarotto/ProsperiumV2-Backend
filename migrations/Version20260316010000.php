<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260316010000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajusta tabela config_params existente para o schema canônico do Prosperium v2.';
    }

    public function up(Schema $schema): void
    {
        // 1) Renomear PK de id_contab_param para id e ajustar tipos
        $this->addSql("
            ALTER TABLE config_params
                CHANGE COLUMN id_contab_param id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                ADD COLUMN company_id BIGINT UNSIGNED NOT NULL DEFAULT 8 AFTER id,
                MODIFY COLUMN name VARCHAR(255) NOT NULL,
                MODIFY COLUMN `type` VARCHAR(100) DEFAULT NULL,
                MODIFY COLUMN value TEXT NOT NULL,
                MODIFY COLUMN description TEXT DEFAULT NULL,
                MODIFY COLUMN status SMALLINT NOT NULL DEFAULT 1,
                MODIFY COLUMN `restrict` SMALLINT NOT NULL DEFAULT 2,
                ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)',
                ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)'
        ");

        // 2) Indices e FK
        $this->addSql('ALTER TABLE config_params ADD INDEX idx_config_params_company_status (company_id, status)');
        $this->addSql('ALTER TABLE config_params ADD CONSTRAINT uk_config_params_company_name UNIQUE (company_id, name)');
        $this->addSql('ALTER TABLE config_params ADD CONSTRAINT FK_CONFIG_PARAMS_COMPANY FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE');

        // 3) Permissoes para o módulo de parametrização
        $this->addSql("
            INSERT INTO permissoes (modulo_id, codigo, nome, status)
            SELECT id, 'admin.parametrizacao_sistema.view', 'Visualizar parametrização do sistema', 'active'
            FROM modulos WHERE codigo = 'admin'
            AND NOT EXISTS (SELECT 1 FROM permissoes WHERE codigo = 'admin.parametrizacao_sistema.view')
        ");

        $this->addSql("
            INSERT INTO permissoes (modulo_id, codigo, nome, status)
            SELECT id, 'admin.parametrizacao_sistema.create_edit', 'Criar/editar parametrização do sistema', 'active'
            FROM modulos WHERE codigo = 'admin'
            AND NOT EXISTS (SELECT 1 FROM permissoes WHERE codigo = 'admin.parametrizacao_sistema.create_edit')
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM permissoes WHERE codigo IN ('admin.parametrizacao_sistema.view', 'admin.parametrizacao_sistema.create_edit')");

        $this->addSql('ALTER TABLE config_params DROP FOREIGN KEY FK_CONFIG_PARAMS_COMPANY');
        $this->addSql('ALTER TABLE config_params DROP INDEX uk_config_params_company_name');
        $this->addSql('ALTER TABLE config_params DROP INDEX idx_config_params_company_status');

        $this->addSql("
            ALTER TABLE config_params
                DROP COLUMN created_at,
                DROP COLUMN updated_at,
                DROP COLUMN company_id,
                CHANGE COLUMN id id_contab_param INT NOT NULL AUTO_INCREMENT,
                MODIFY COLUMN name CHAR(20) NOT NULL,
                MODIFY COLUMN `type` VARCHAR(100) NOT NULL DEFAULT 'Geral',
                MODIFY COLUMN value VARCHAR(100) NOT NULL,
                MODIFY COLUMN description VARCHAR(255) DEFAULT NULL,
                MODIFY COLUMN status TINYINT NOT NULL DEFAULT 1,
                MODIFY COLUMN `restrict` INT NOT NULL DEFAULT 1
        ");
    }
}
