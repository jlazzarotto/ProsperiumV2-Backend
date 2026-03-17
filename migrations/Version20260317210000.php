<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Remove empresas and unidades_negocio from control DB.
 * These tables now live exclusively in the tenant DB.
 * Drops all FK constraints referencing them before dropping the tables.
 */
final class Version20260317210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop empresas and unidades_negocio from control DB (moved to tenant DB only).';
    }

    public function up(Schema $schema): void
    {
        // Drop FK constraints referencing empresas
        $this->addSql('ALTER TABLE auditoria_logs DROP FOREIGN KEY fk_auditoria_logs_empresa');
        $this->addSql('ALTER TABLE user_alcadas DROP FOREIGN KEY fk_user_alcadas_empresa');
        $this->addSql('ALTER TABLE user_empresas DROP FOREIGN KEY fk_user_empresas_empresa');
        $this->addSql('ALTER TABLE user_perfis_acesso DROP FOREIGN KEY fk_user_perfis_empresa');

        // Drop FK constraints referencing unidades_negocio
        $this->addSql('ALTER TABLE auditoria_logs DROP FOREIGN KEY fk_auditoria_logs_unidade');
        $this->addSql('ALTER TABLE user_alcadas DROP FOREIGN KEY fk_user_alcadas_unidade');
        $this->addSql('ALTER TABLE user_perfis_acesso DROP FOREIGN KEY fk_user_perfis_unidade');
        $this->addSql('ALTER TABLE user_unidades DROP FOREIGN KEY fk_user_unidades_unidade');

        // Drop the tables
        $this->addSql('DROP TABLE IF EXISTS empresas');
        $this->addSql('DROP TABLE IF EXISTS unidades_negocio');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("CREATE TABLE unidades_negocio (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            company_id BIGINT UNSIGNED NOT NULL,
            nome VARCHAR(255) NOT NULL,
            abreviatura VARCHAR(50) NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE KEY uk_unidades_negocio_company_nome (company_id, nome),
            UNIQUE KEY uk_unidades_negocio_company_abrev (company_id, abreviatura),
            CONSTRAINT fk_unidades_negocio_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE empresas (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            company_id BIGINT UNSIGNED NOT NULL,
            razao_social VARCHAR(255) NOT NULL,
            nome_fantasia VARCHAR(255) DEFAULT NULL,
            apelido VARCHAR(255) DEFAULT NULL,
            abreviatura VARCHAR(50) DEFAULT NULL,
            cnpj VARCHAR(20) DEFAULT NULL,
            inscricao_estadual VARCHAR(50) DEFAULT NULL,
            inscricao_municipal VARCHAR(50) DEFAULT NULL,
            cep VARCHAR(9) DEFAULT NULL,
            estado VARCHAR(50) DEFAULT NULL,
            cidade VARCHAR(100) DEFAULT NULL,
            logradouro VARCHAR(255) DEFAULT NULL,
            numero VARCHAR(20) DEFAULT NULL,
            complemento VARCHAR(100) DEFAULT NULL,
            bairro VARCHAR(100) DEFAULT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            deleted_at DATETIME DEFAULT NULL,
            UNIQUE KEY uk_empresas_company_cnpj (company_id, cnpj),
            INDEX idx_empresas_company (company_id),
            CONSTRAINT fk_empresas_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        // Restore FK constraints
        $this->addSql('ALTER TABLE auditoria_logs ADD CONSTRAINT fk_auditoria_logs_empresa FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE auditoria_logs ADD CONSTRAINT fk_auditoria_logs_unidade FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_alcadas ADD CONSTRAINT fk_user_alcadas_empresa FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_alcadas ADD CONSTRAINT fk_user_alcadas_unidade FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_empresas ADD CONSTRAINT fk_user_empresas_empresa FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_perfis_acesso ADD CONSTRAINT fk_user_perfis_empresa FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_perfis_acesso ADD CONSTRAINT fk_user_perfis_unidade FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_unidades ADD CONSTRAINT fk_user_unidades_unidade FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE');
    }
}
