<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260316050000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Register admin.provisionar_tenant module, permissions, and assign to company_admin profile.';
    }

    public function up(Schema $schema): void
    {
        // 1. Insert module
        $this->addSql(
            "INSERT INTO modulos (codigo, nome, categoria_codigo, categoria_nome, menu_label, route_path, icon_key, sort_order, is_menu_entry, status)
             SELECT 'admin.provisionar_tenant', 'Provisionar Tenant', 'admin', 'Administrador', 'Provisionar Tenant', '/admin/provisionar-tenant', 'database-zap', 65, 1, 'active'
             WHERE NOT EXISTS (SELECT 1 FROM modulos WHERE codigo = 'admin.provisionar_tenant')"
        );

        // 2. Insert permissions (view, create_edit, delete)
        $this->addSql(
            "INSERT INTO permissoes (modulo_id, codigo, nome, status)
             SELECT id, 'admin.provisionar_tenant.view', 'Visualizar Provisionar Tenant', 'active'
             FROM modulos
             WHERE codigo = 'admin.provisionar_tenant'
               AND NOT EXISTS (SELECT 1 FROM permissoes WHERE codigo = 'admin.provisionar_tenant.view')"
        );

        $this->addSql(
            "INSERT INTO permissoes (modulo_id, codigo, nome, status)
             SELECT id, 'admin.provisionar_tenant.create_edit', 'Criar/Editar Provisionar Tenant', 'active'
             FROM modulos
             WHERE codigo = 'admin.provisionar_tenant'
               AND NOT EXISTS (SELECT 1 FROM permissoes WHERE codigo = 'admin.provisionar_tenant.create_edit')"
        );

        $this->addSql(
            "INSERT INTO permissoes (modulo_id, codigo, nome, status)
             SELECT id, 'admin.provisionar_tenant.delete', 'Excluir Provisionar Tenant', 'active'
             FROM modulos
             WHERE codigo = 'admin.provisionar_tenant'
               AND NOT EXISTS (SELECT 1 FROM permissoes WHERE codigo = 'admin.provisionar_tenant.delete')"
        );

        // 3. Assign permissions to company_admin profile
        $this->addSql(
            "INSERT INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
             SELECT p.id, perm.id
             FROM perfis_acesso p
             JOIN permissoes perm ON perm.codigo LIKE 'admin.provisionar_tenant.%'
             WHERE p.codigo = 'company_admin'
               AND NOT EXISTS (
                   SELECT 1
                   FROM perfil_acesso_permissoes x
                   WHERE x.perfil_acesso_id = p.id
                     AND x.permissao_id = perm.id
             )"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE pap FROM perfil_acesso_permissoes pap JOIN permissoes perm ON perm.id = pap.permissao_id WHERE perm.codigo LIKE 'admin.provisionar_tenant.%'");
        $this->addSql("DELETE FROM permissoes WHERE codigo LIKE 'admin.provisionar_tenant.%'");
        $this->addSql("DELETE FROM modulos WHERE codigo = 'admin.provisionar_tenant'");
    }
}
