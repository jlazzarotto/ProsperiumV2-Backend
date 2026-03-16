<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260316030000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cadastro de usuarios - ajusta schema users conforme RF-010/RF-011/RF-012 (company_id FK, mfa, status pt-BR, login tracking, audit login).';
    }

    public function up(Schema $schema): void
    {
        // 1. Adicionar company_id (nullable primeiro para não quebrar dados existentes)
        $this->addSql("ALTER TABLE users ADD COLUMN company_id BIGINT UNSIGNED DEFAULT NULL AFTER id");

        // 2. Adicionar mfa_habilitado
        $this->addSql("ALTER TABLE users ADD COLUMN mfa_habilitado TINYINT(1) NOT NULL DEFAULT 0 AFTER password_hash");

        // 3. Adicionar mfa_secret para TOTP
        $this->addSql("ALTER TABLE users ADD COLUMN mfa_secret VARCHAR(255) DEFAULT NULL AFTER mfa_habilitado");

        // 4. Adicionar ultimo_login
        $this->addSql("ALTER TABLE users ADD COLUMN ultimo_login DATETIME DEFAULT NULL AFTER mfa_secret");

        // 5. Adicionar failed_login_attempts e locked_until para controle de brute force
        $this->addSql("ALTER TABLE users ADD COLUMN failed_login_attempts INT UNSIGNED NOT NULL DEFAULT 0 AFTER ultimo_login");
        $this->addSql("ALTER TABLE users ADD COLUMN locked_until DATETIME DEFAULT NULL AFTER failed_login_attempts");

        // 6. Migrar status de inglês para português
        $this->addSql("UPDATE users SET status = 'ativo' WHERE status = 'active'");
        $this->addSql("UPDATE users SET status = 'inativo' WHERE status = 'inactive'");
        $this->addSql("ALTER TABLE users MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'ativo'");

        // 7. Vincular users existentes à primeira company do user_companies
        $this->addSql("UPDATE users u
            INNER JOIN user_companies uc ON uc.user_id = u.id
            SET u.company_id = uc.company_id
            WHERE u.company_id IS NULL AND u.role != 'ROLE_ROOT'");

        // 8. Para ROLE_ROOT sem company, vincular à primeira company existente (se houver)
        $this->addSql("UPDATE users u
            SET u.company_id = (SELECT MIN(id) FROM companies)
            WHERE u.company_id IS NULL AND u.role = 'ROLE_ROOT'
            AND EXISTS (SELECT 1 FROM companies)");

        // 9. Remover unique constraint global de email
        $this->addSql("ALTER TABLE users DROP INDEX uk_users_email");

        // 10. Adicionar FK para company_id
        $this->addSql("ALTER TABLE users
            ADD CONSTRAINT fk_users_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE");

        // 11. Adicionar unique constraint por company (email unico por company)
        $this->addSql("CREATE UNIQUE INDEX uk_users_company_email ON users (company_id, email)");

        // 12. Adicionar indice para lookup de login
        $this->addSql("CREATE INDEX idx_users_email_status ON users (email, status)");

        // 13. Atualizar status nas tabelas associadas
        $this->addSql("UPDATE user_companies SET status = 'ativo' WHERE status = 'active'");
        $this->addSql("UPDATE user_companies SET status = 'inativo' WHERE status = 'inactive'");

        $this->addSql("UPDATE user_empresas SET status = 'ativo' WHERE status = 'active'");
        $this->addSql("UPDATE user_empresas SET status = 'inativo' WHERE status = 'inactive'");

        $this->addSql("UPDATE user_unidades SET status = 'ativo' WHERE status = 'active'");
        $this->addSql("UPDATE user_unidades SET status = 'inativo' WHERE status = 'inactive'");

        // 14. Adicionar módulo admin.cadastro_usuarios ao menu
        $this->addSql("INSERT INTO modulos (codigo, nome, status, categoria_codigo, categoria_nome, menu_label, route_path, icon_key, sort_order, is_menu_entry)
            VALUES ('admin.cadastro_usuarios', 'Cadastro de Usuarios', 'active', 'admin', 'Administrador', 'Usuarios', '/admin/cadastro-usuarios', 'Users', 15, 1)
            ON DUPLICATE KEY UPDATE nome = VALUES(nome)");

        // 15. Adicionar permissões do módulo
        $this->addSql("INSERT INTO permissoes (modulo_id, codigo, nome, status)
            SELECT id, 'admin.cadastro_usuarios.view', 'Visualizar usuarios', 'active' FROM modulos WHERE codigo = 'admin.cadastro_usuarios'
            ON DUPLICATE KEY UPDATE nome = VALUES(nome)");

        $this->addSql("INSERT INTO permissoes (modulo_id, codigo, nome, status)
            SELECT id, 'admin.cadastro_usuarios.create_edit', 'Criar e editar usuarios', 'active' FROM modulos WHERE codigo = 'admin.cadastro_usuarios'
            ON DUPLICATE KEY UPDATE nome = VALUES(nome)");

        $this->addSql("INSERT INTO permissoes (modulo_id, codigo, nome, status)
            SELECT id, 'admin.cadastro_usuarios.delete', 'Deletar usuarios', 'active' FROM modulos WHERE codigo = 'admin.cadastro_usuarios'
            ON DUPLICATE KEY UPDATE nome = VALUES(nome)");

        // 16. Associar permissões ao perfil company_admin
        $this->addSql("INSERT IGNORE INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
            SELECT p.id, perm.id
            FROM perfis_acesso p
            CROSS JOIN permissoes perm
            WHERE p.codigo = 'company_admin'
            AND perm.codigo LIKE 'admin.cadastro_usuarios.%'");
    }

    public function down(Schema $schema): void
    {
        // Remover permissões do módulo
        $this->addSql("DELETE pap FROM perfil_acesso_permissoes pap
            INNER JOIN permissoes perm ON pap.permissao_id = perm.id
            WHERE perm.codigo LIKE 'admin.cadastro_usuarios.%'");

        $this->addSql("DELETE FROM permissoes WHERE codigo LIKE 'admin.cadastro_usuarios.%'");
        $this->addSql("DELETE FROM modulos WHERE codigo = 'admin.cadastro_usuarios'");

        // Reverter status
        $this->addSql("UPDATE user_unidades SET status = 'active' WHERE status = 'ativo'");
        $this->addSql("UPDATE user_unidades SET status = 'inactive' WHERE status = 'inativo'");
        $this->addSql("UPDATE user_empresas SET status = 'active' WHERE status = 'ativo'");
        $this->addSql("UPDATE user_empresas SET status = 'inactive' WHERE status = 'inativo'");
        $this->addSql("UPDATE user_companies SET status = 'active' WHERE status = 'ativo'");
        $this->addSql("UPDATE user_companies SET status = 'inactive' WHERE status = 'inativo'");

        $this->addSql("DROP INDEX idx_users_email_status ON users");
        $this->addSql("DROP INDEX uk_users_company_email ON users");
        $this->addSql("ALTER TABLE users DROP FOREIGN KEY fk_users_company");
        $this->addSql("CREATE UNIQUE INDEX uk_users_email ON users (email)");

        $this->addSql("UPDATE users SET status = 'active' WHERE status = 'ativo'");
        $this->addSql("UPDATE users SET status = 'inactive' WHERE status IN ('inativo', 'bloqueado')");
        $this->addSql("ALTER TABLE users MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'active'");

        $this->addSql("ALTER TABLE users DROP COLUMN locked_until");
        $this->addSql("ALTER TABLE users DROP COLUMN failed_login_attempts");
        $this->addSql("ALTER TABLE users DROP COLUMN ultimo_login");
        $this->addSql("ALTER TABLE users DROP COLUMN mfa_secret");
        $this->addSql("ALTER TABLE users DROP COLUMN mfa_habilitado");
        $this->addSql("ALTER TABLE users DROP COLUMN company_id");
    }
}
