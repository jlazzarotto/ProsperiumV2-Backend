<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create identity and access tables with RBAC + ABAC seed data.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE users (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE KEY uk_users_email (email)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE user_companies (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            company_id BIGINT UNSIGNED NOT NULL,
            is_company_admin TINYINT(1) NOT NULL DEFAULT 0,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE KEY uk_user_companies (user_id, company_id),
            CONSTRAINT fk_user_companies_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_companies_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE user_empresas (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            company_id BIGINT UNSIGNED NOT NULL,
            empresa_id BIGINT UNSIGNED NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE KEY uk_user_empresas (user_id, empresa_id),
            CONSTRAINT fk_user_empresas_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_empresas_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_empresas_empresa FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE user_unidades (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            company_id BIGINT UNSIGNED NOT NULL,
            unidade_id BIGINT UNSIGNED NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE KEY uk_user_unidades (user_id, unidade_id),
            CONSTRAINT fk_user_unidades_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_unidades_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_unidades_unidade FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE modulos (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(100) NOT NULL,
            nome VARCHAR(255) NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            UNIQUE KEY uk_modulos_codigo (codigo)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE permissoes (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            modulo_id BIGINT UNSIGNED NOT NULL,
            codigo VARCHAR(120) NOT NULL,
            nome VARCHAR(255) NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            UNIQUE KEY uk_permissoes_codigo (codigo),
            CONSTRAINT fk_permissoes_modulo FOREIGN KEY (modulo_id) REFERENCES modulos (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

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

        $this->addSql("CREATE TABLE user_perfis_acesso (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            company_id BIGINT UNSIGNED NOT NULL,
            perfil_acesso_id BIGINT UNSIGNED NOT NULL,
            empresa_id BIGINT UNSIGNED DEFAULT NULL,
            unidade_id BIGINT UNSIGNED DEFAULT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            UNIQUE KEY uk_user_perfis (user_id, perfil_acesso_id, empresa_id, unidade_id),
            CONSTRAINT fk_user_perfis_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_perfis_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_perfis_perfil FOREIGN KEY (perfil_acesso_id) REFERENCES perfis_acesso (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_perfis_empresa FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_perfis_unidade FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE user_alcadas (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            company_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            empresa_id BIGINT UNSIGNED DEFAULT NULL,
            unidade_id BIGINT UNSIGNED DEFAULT NULL,
            tipo_operacao VARCHAR(100) NOT NULL,
            valor_limite DECIMAL(18,2) DEFAULT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            KEY idx_user_alcadas_lookup (company_id, user_id, empresa_id, unidade_id, tipo_operacao, status),
            CONSTRAINT fk_user_alcadas_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_alcadas_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_alcadas_empresa FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
            CONSTRAINT fk_user_alcadas_unidade FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("INSERT INTO modulos (codigo, nome, status) VALUES
            ('identity', 'Identity & Access', 'active'),
            ('company', 'Core SaaS', 'active')");

        $this->addSql("INSERT INTO permissoes (modulo_id, codigo, nome, status)
            SELECT id, 'identity.users.create', 'Criar usuários', 'active' FROM modulos WHERE codigo = 'identity'");
        $this->addSql("INSERT INTO permissoes (modulo_id, codigo, nome, status)
            SELECT id, 'identity.users.view', 'Listar usuários', 'active' FROM modulos WHERE codigo = 'identity'");
        $this->addSql("INSERT INTO permissoes (modulo_id, codigo, nome, status)
            SELECT id, 'identity.perfis.create', 'Criar perfis', 'active' FROM modulos WHERE codigo = 'identity'");
        $this->addSql("INSERT INTO permissoes (modulo_id, codigo, nome, status)
            SELECT id, 'identity.permissoes.view', 'Listar permissões', 'active' FROM modulos WHERE codigo = 'identity'");

        $this->addSql("INSERT INTO perfis_acesso (company_id, codigo, nome, tipo, status)
            VALUES (NULL, 'company_admin', 'Administrador da Company', 'system', 'active')");

        $this->addSql("INSERT INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
            SELECT p.id, perm.id
            FROM perfis_acesso p
            CROSS JOIN permissoes perm
            WHERE p.codigo = 'company_admin'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS user_alcadas');
        $this->addSql('DROP TABLE IF EXISTS user_perfis_acesso');
        $this->addSql('DROP TABLE IF EXISTS perfil_acesso_permissoes');
        $this->addSql('DROP TABLE IF EXISTS perfis_acesso');
        $this->addSql('DROP TABLE IF EXISTS permissoes');
        $this->addSql('DROP TABLE IF EXISTS modulos');
        $this->addSql('DROP TABLE IF EXISTS user_unidades');
        $this->addSql('DROP TABLE IF EXISTS user_empresas');
        $this->addSql('DROP TABLE IF EXISTS user_companies');
        $this->addSql('DROP TABLE IF EXISTS users');
    }
}
