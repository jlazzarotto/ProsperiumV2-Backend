<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create canonical core SaaS tables for companies, tenant instances, empresas, unidades de negocio and auditoria logs.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS auditoria_logs');
        $this->addSql('DROP TABLE IF EXISTS tenant_instances');
        $this->addSql('DROP TABLE IF EXISTS unidades_negocio');
        $this->addSql('DROP TABLE IF EXISTS empresas');
        $this->addSql('DROP TABLE IF EXISTS companies');

        $this->addSql(
            "CREATE TABLE companies (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(255) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                UNIQUE KEY uk_companies_nome (nome)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );

        $this->addSql(
            "CREATE TABLE tenant_instances (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                tenancy_mode VARCHAR(20) NOT NULL,
                database_key VARCHAR(100) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                UNIQUE KEY uk_tenant_instances_company (company_id),
                UNIQUE KEY uk_tenant_instances_dbkey (database_key),
                CONSTRAINT fk_tenant_instances_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );

        $this->addSql(
            "CREATE TABLE empresas (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                razao_social VARCHAR(255) NOT NULL,
                nome_fantasia VARCHAR(255) DEFAULT NULL,
                cnpj VARCHAR(20) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                UNIQUE KEY uk_empresas_company_cnpj (company_id, cnpj),
                CONSTRAINT fk_empresas_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );

        $this->addSql(
            "CREATE TABLE unidades_negocio (
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
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );

        $this->addSql(
            "CREATE TABLE auditoria_logs (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED DEFAULT NULL,
                recurso VARCHAR(100) NOT NULL,
                acao VARCHAR(120) NOT NULL,
                payload_json JSON DEFAULT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                KEY idx_auditoria_logs_lookup (company_id, recurso, acao, created_at),
                CONSTRAINT fk_auditoria_logs_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS auditoria_logs');
        $this->addSql('DROP TABLE IF EXISTS tenant_instances');
        $this->addSql('DROP TABLE IF EXISTS unidades_negocio');
        $this->addSql('DROP TABLE IF EXISTS empresas');
        $this->addSql('DROP TABLE IF EXISTS companies');
    }
}
