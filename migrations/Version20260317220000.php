<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Remove perfis_acesso and perfil_acesso_permissoes from control DB.
 * These tables now live exclusively in each tenant DB.
 * Drops FK constraints referencing perfis_acesso before dropping the tables.
 */
final class Version20260317220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop perfis_acesso and perfil_acesso_permissoes from control DB (moved to tenant DB).';
    }

    public function up(Schema $schema): void
    {
        // Drop FK constraint from user_perfis_acesso referencing perfis_acesso
        $this->addSql('ALTER TABLE user_perfis_acesso DROP FOREIGN KEY fk_user_perfis_perfil');

        // Drop the tables
        $this->addSql('DROP TABLE IF EXISTS perfil_acesso_permissoes');
        $this->addSql('DROP TABLE IF EXISTS perfis_acesso');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("CREATE TABLE perfis_acesso (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            company_id BIGINT UNSIGNED DEFAULT NULL,
            codigo VARCHAR(100) NOT NULL,
            nome VARCHAR(255) NOT NULL,
            tipo VARCHAR(20) NOT NULL DEFAULT 'custom',
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            UNIQUE KEY uk_perfis_acesso_company_codigo (company_id, codigo),
            CONSTRAINT fk_perfis_acesso_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE perfil_acesso_permissoes (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            perfil_acesso_id BIGINT UNSIGNED NOT NULL,
            permissao_id BIGINT UNSIGNED NOT NULL,
            UNIQUE KEY uk_pap (perfil_acesso_id, permissao_id),
            CONSTRAINT fk_pap_perfil FOREIGN KEY (perfil_acesso_id) REFERENCES perfis_acesso (id) ON DELETE CASCADE,
            CONSTRAINT fk_pap_permissao FOREIGN KEY (permissao_id) REFERENCES permissoes (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql('ALTER TABLE user_perfis_acesso ADD CONSTRAINT fk_user_perfis_perfil FOREIGN KEY (perfil_acesso_id) REFERENCES perfis_acesso (id) ON DELETE CASCADE');
    }
}
