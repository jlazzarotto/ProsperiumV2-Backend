<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314300000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add navigation metadata to modules, seed menu-driven access catalog, and backfill company_admin profiles.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $columns = $schemaManager->listTableColumns('modulos');

        if (!isset($columns['categoria_codigo'])) {
            $this->addSql("ALTER TABLE modulos ADD categoria_codigo VARCHAR(100) NOT NULL DEFAULT 'sistema'");
        }

        if (!isset($columns['categoria_nome'])) {
            $this->addSql("ALTER TABLE modulos ADD categoria_nome VARCHAR(255) NOT NULL DEFAULT 'Sistema'");
        }

        if (!isset($columns['menu_label'])) {
            $this->addSql("ALTER TABLE modulos ADD menu_label VARCHAR(255) DEFAULT NULL");
        }

        if (!isset($columns['route_path'])) {
            $this->addSql("ALTER TABLE modulos ADD route_path VARCHAR(255) DEFAULT NULL");
        }

        if (!isset($columns['icon_key'])) {
            $this->addSql("ALTER TABLE modulos ADD icon_key VARCHAR(100) DEFAULT NULL");
        }

        if (!isset($columns['sort_order'])) {
            $this->addSql("ALTER TABLE modulos ADD sort_order INT NOT NULL DEFAULT 0");
        }

        if (!isset($columns['is_menu_entry'])) {
            $this->addSql("ALTER TABLE modulos ADD is_menu_entry TINYINT(1) NOT NULL DEFAULT 1");
        }

        $modules = [
            ['financeiro.dashboard', 'Dashboard Home', 'financeiro', 'Financeiro', 'Dashboard', '/', 'layout-grid', 10],
            ['financeiro.visao_inicial', 'Visão Inicial do Sistema', 'financeiro', 'Financeiro', 'Visão inicial', '/financeiro', 'panel-top', 20],
            ['admin.coordenar_empresas', 'Coordenar Empresas', 'admin', 'Administrador', 'Coordenar Empresas', '/admin/coordenar-empresas', 'building-2', 10],
            ['admin.coordenar_unidades', 'Coordenar Unidades', 'admin', 'Administrador', 'Coordenar Unidades', '/admin/coordenar-unidades', 'building', 20],
            ['admin.parametrizacao_sistema', 'Parametrização do Sistema', 'admin', 'Administrador', 'Parametrização do sistema', '/admin/parametrizacao-sistema', 'sliders-horizontal', 30],
            ['admin.permissoes', 'Permissões', 'admin', 'Administrador', 'Permissões', '/admin/permissoes', 'shield', 40],
            ['admin.logs_auditoria', 'Logs de Auditoria', 'admin', 'Administrador', 'Logs de Auditoria', '/admin/logs', 'activity', 50],
            ['admin.importacao_dados', 'Importação de Dados', 'admin', 'Administrador', 'Importação de Dados', '/admin/importacao-dados', 'database-zap', 60],
            ['config.contabilidade', 'Configurações de Contabilidade', 'configuracoes', 'Configurações', 'Contabilidade', '/configuracoes/contabilidade', 'book-open-text', 10],
            ['config.dre', 'Configurar DRE', 'configuracoes', 'Configurações', 'Configurar DRE', '/configuracoes/dre', 'line-chart', 20],
            ['config.centro_custo', 'Centro de Custo', 'configuracoes', 'Configurações', 'Centro de Custo', '/configuracoes/centro-custo', 'waypoints', 30],
            ['cadastros.agencias_bancarias', 'Agência Bancária', 'cadastros', 'Cadastros', 'Agência Bancária', '/cadastros/agencias-bancarias', 'landmark', 10],
            ['cadastros.contas_caixa', 'Contas Caixa', 'cadastros', 'Cadastros', 'Contas Caixa', '/cadastros/contas-caixa', 'wallet', 20],
            ['cadastros.formas_pagamento', 'Forma de Pagamento', 'cadastros', 'Cadastros', 'Forma de Pagamento', '/cadastros/formas-pagamento', 'credit-card', 30],
            ['cadastros.pessoas', 'Pessoa', 'cadastros', 'Cadastros', 'Pessoa', '/cadastros/pessoas', 'users', 40],
            ['financeiro.lancamentos', 'Lançamentos', 'financeiro', 'Financeiro', 'Lançamentos', '/financeiro/lancamentos', 'receipt', 30],
            ['financeiro.transferencias', 'Transferências entre contas', 'financeiro', 'Financeiro', 'Transf. entre contas', '/financeiro/transferencias', 'arrow-left-right', 40],
            ['financeiro.cartoes_credito', 'Cartões de Crédito', 'financeiro', 'Financeiro', 'Cartões de Crédito', '/financeiro/cartoes-credito', 'credit-card', 50],
            ['relatorios.dre', 'Relatório DRE', 'relatorios', 'Relatórios', 'DRE', '/relatorios/dre', 'chart-column', 10],
            ['relatorios.movimento_contabilidade', 'Movimento Contabilidade', 'relatorios', 'Relatórios', 'Movimento contabilidade', '/relatorios/movimento-contabilidade', 'scroll-text', 20],
            ['relatorios.fluxo_caixa', 'Fluxo de Caixa', 'relatorios', 'Relatórios', 'Fluxo de caixa', '/relatorios/fluxo-caixa', 'chart-no-axes-combined', 30],
            ['asaas.cobrancas', 'Boletos e Cobranças', 'asaas', 'Asaas', 'Boletos e Cobranças', '/asaas/cobrancas', 'badge-dollar-sign', 10],
            ['asaas.notas_fiscais', 'Notas Fiscais', 'asaas', 'Asaas', 'Notas Fiscais', '/asaas/notas-fiscais', 'file-text', 20],
            ['asaas.configuracoes', 'Configurações Asaas', 'asaas', 'Asaas', 'Configurações', '/asaas/configuracoes', 'settings-2', 30],
        ];

        foreach ($modules as [$codigo, $nome, $categoriaCodigo, $categoriaNome, $menuLabel, $routePath, $iconKey, $sortOrder]) {
            $this->addSql(sprintf(
                "INSERT INTO modulos (codigo, nome, categoria_codigo, categoria_nome, menu_label, route_path, icon_key, sort_order, is_menu_entry, status)
                 SELECT '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, 1, 'active'
                 WHERE NOT EXISTS (SELECT 1 FROM modulos WHERE codigo = '%s')",
                $codigo,
                $nome,
                $categoriaCodigo,
                $categoriaNome,
                $menuLabel,
                $routePath,
                $iconKey,
                $sortOrder,
                $codigo
            ));

            $this->addSql(sprintf(
                "UPDATE modulos
                 SET nome = '%s',
                     categoria_codigo = '%s',
                     categoria_nome = '%s',
                     menu_label = '%s',
                     route_path = '%s',
                     icon_key = '%s',
                     sort_order = %d,
                     is_menu_entry = 1,
                     status = 'active'
                 WHERE codigo = '%s'",
                $nome,
                $categoriaCodigo,
                $categoriaNome,
                $menuLabel,
                $routePath,
                $iconKey,
                $sortOrder,
                $codigo
            ));

            foreach ([
                [$codigo . '.view', 'Visualizar ' . $nome],
                [$codigo . '.create_edit', 'Criar/Editar ' . $nome],
                [$codigo . '.delete', 'Excluir ' . $nome],
            ] as [$permissionCode, $permissionName]) {
                $this->addSql(sprintf(
                    "INSERT INTO permissoes (modulo_id, codigo, nome, status)
                     SELECT id, '%s', '%s', 'active'
                     FROM modulos
                     WHERE codigo = '%s'
                       AND NOT EXISTS (SELECT 1 FROM permissoes WHERE codigo = '%s')",
                    $permissionCode,
                    $permissionName,
                    $codigo,
                    $permissionCode
                ));
            }

            $this->addSql(sprintf(
                "INSERT INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
                 SELECT p.id, perm.id
                 FROM perfis_acesso p
                 JOIN permissoes perm ON perm.codigo LIKE '%s.%%'
                 WHERE p.codigo = 'company_admin'
                   AND NOT EXISTS (
                       SELECT 1
                       FROM perfil_acesso_permissoes x
                       WHERE x.perfil_acesso_id = p.id
                         AND x.permissao_id = perm.id
                 )",
                $codigo
            ));
        }

        $this->addSql("UPDATE modulos SET categoria_codigo = 'legacy', categoria_nome = 'Legado', is_menu_entry = 0 WHERE codigo IN ('identity', 'company', 'cadastro', 'contabil', 'cobranca', 'tesouraria', 'bpo', 'financeiro')");

        $this->addSql("INSERT INTO user_perfis_acesso (user_id, company_id, perfil_acesso_id, empresa_id, unidade_id, status)
            SELECT uc.user_id, uc.company_id, p.id, NULL, NULL, 'active'
            FROM user_companies uc
            JOIN users u ON u.id = uc.user_id AND u.role = 'ROLE_ADMIN'
            JOIN perfis_acesso p ON p.codigo = 'company_admin'
            LEFT JOIN user_perfis_acesso up
              ON up.user_id = uc.user_id
             AND up.company_id = uc.company_id
             AND up.perfil_acesso_id = p.id
             AND up.empresa_id IS NULL
             AND up.unidade_id IS NULL
            WHERE up.id IS NULL");
    }

    public function down(Schema $schema): void
    {
        $moduleCodes = [
            'financeiro.dashboard',
            'financeiro.visao_inicial',
            'admin.coordenar_empresas',
            'admin.coordenar_unidades',
            'admin.parametrizacao_sistema',
            'admin.permissoes',
            'admin.logs_auditoria',
            'admin.importacao_dados',
            'config.contabilidade',
            'config.dre',
            'config.centro_custo',
            'cadastros.agencias_bancarias',
            'cadastros.contas_caixa',
            'cadastros.formas_pagamento',
            'cadastros.pessoas',
            'financeiro.lancamentos',
            'financeiro.transferencias',
            'financeiro.cartoes_credito',
            'relatorios.dre',
            'relatorios.movimento_contabilidade',
            'relatorios.fluxo_caixa',
            'asaas.cobrancas',
            'asaas.notas_fiscais',
            'asaas.configuracoes',
        ];

        foreach ($moduleCodes as $code) {
            $this->addSql("DELETE FROM perfil_acesso_permissoes WHERE permissao_id IN (SELECT id FROM permissoes WHERE codigo LIKE '{$code}.%')");
            $this->addSql("DELETE FROM permissoes WHERE codigo LIKE '{$code}.%'");
            $this->addSql("DELETE FROM modulos WHERE codigo = '{$code}'");
        }
    }
}
