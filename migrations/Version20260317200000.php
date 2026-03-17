<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Move paises, ufs, municipios from tenant DB to control DB.
 * These reference tables are now shared across all tenants and live in the control plane.
 */
final class Version20260317200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create paises, ufs, municipios in control DB (moved from tenant DB).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE IF NOT EXISTS paises (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            codigo_m49 INT NOT NULL,
            iso_alpha2 VARCHAR(2) NOT NULL,
            iso_alpha3 VARCHAR(3) NOT NULL,
            nome VARCHAR(180) NOT NULL,
            regiao_codigo_m49 INT DEFAULT NULL,
            regiao_nome VARCHAR(120) DEFAULT NULL,
            sub_regiao_codigo_m49 INT DEFAULT NULL,
            sub_regiao_nome VARCHAR(160) DEFAULT NULL,
            regiao_intermediaria_codigo_m49 INT DEFAULT NULL,
            regiao_intermediaria_nome VARCHAR(160) DEFAULT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            hash_payload VARCHAR(64) NOT NULL,
            origem_dados VARCHAR(30) NOT NULL,
            sincronizado_em DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE KEY uk_paises_codigo_m49 (codigo_m49),
            UNIQUE KEY uk_paises_iso_alpha2 (iso_alpha2),
            UNIQUE KEY uk_paises_iso_alpha3 (iso_alpha3),
            INDEX idx_paises_status (status)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE IF NOT EXISTS ufs (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            codigo_ibge INT NOT NULL,
            sigla VARCHAR(2) NOT NULL,
            nome VARCHAR(60) NOT NULL,
            regiao_codigo_ibge INT NOT NULL,
            regiao_sigla VARCHAR(2) NOT NULL,
            regiao_nome VARCHAR(40) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            hash_payload VARCHAR(64) NOT NULL,
            origem_dados VARCHAR(30) NOT NULL,
            sincronizado_em DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE KEY uk_ufs_codigo_ibge (codigo_ibge),
            UNIQUE KEY uk_ufs_sigla (sigla),
            INDEX idx_ufs_status (status)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE IF NOT EXISTS municipios (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            codigo_ibge BIGINT UNSIGNED NOT NULL,
            nome VARCHAR(180) NOT NULL,
            uf_codigo_ibge INT NOT NULL,
            uf_sigla VARCHAR(2) NOT NULL,
            uf_nome VARCHAR(60) NOT NULL,
            regiao_codigo_ibge INT NOT NULL,
            regiao_sigla VARCHAR(2) NOT NULL,
            regiao_nome VARCHAR(40) NOT NULL,
            regiao_intermediaria_codigo_ibge INT DEFAULT NULL,
            regiao_intermediaria_nome VARCHAR(120) DEFAULT NULL,
            regiao_imediata_codigo_ibge INT DEFAULT NULL,
            regiao_imediata_nome VARCHAR(120) DEFAULT NULL,
            microrregiao_codigo_ibge INT DEFAULT NULL,
            microrregiao_nome VARCHAR(120) DEFAULT NULL,
            mesorregiao_codigo_ibge INT DEFAULT NULL,
            mesorregiao_nome VARCHAR(120) DEFAULT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            hash_payload VARCHAR(64) NOT NULL,
            origem_dados VARCHAR(30) NOT NULL,
            sincronizado_em DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE KEY uk_municipios_codigo_ibge (codigo_ibge),
            INDEX idx_municipios_uf_status (uf_sigla, status)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS municipios');
        $this->addSql('DROP TABLE IF EXISTS ufs');
        $this->addSql('DROP TABLE IF EXISTS paises');
    }
}
