<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315030000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create municipios table for IBGE sync.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE municipios (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, codigo_ibge BIGINT UNSIGNED NOT NULL, nome VARCHAR(180) NOT NULL, uf_codigo_ibge INT NOT NULL, uf_sigla VARCHAR(2) NOT NULL, uf_nome VARCHAR(60) NOT NULL, regiao_codigo_ibge INT NOT NULL, regiao_sigla VARCHAR(2) NOT NULL, regiao_nome VARCHAR(40) NOT NULL, regiao_intermediaria_codigo_ibge INT DEFAULT NULL, regiao_intermediaria_nome VARCHAR(120) DEFAULT NULL, regiao_imediata_codigo_ibge INT DEFAULT NULL, regiao_imediata_nome VARCHAR(120) DEFAULT NULL, microrregiao_codigo_ibge INT DEFAULT NULL, microrregiao_nome VARCHAR(120) DEFAULT NULL, mesorregiao_codigo_ibge INT DEFAULT NULL, mesorregiao_nome VARCHAR(120) DEFAULT NULL, status VARCHAR(20) NOT NULL DEFAULT 'active', hash_payload VARCHAR(64) NOT NULL, origem_dados VARCHAR(30) NOT NULL, sincronizado_em DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE KEY uk_municipios_codigo_ibge (codigo_ibge), INDEX idx_municipios_uf_status (uf_sigla, status)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS municipios');
    }
}
