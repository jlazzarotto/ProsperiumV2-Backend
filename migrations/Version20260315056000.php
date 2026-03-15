<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315056000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove permissões legadas cadastro.* e transfere vínculos para cadastros.*.';
    }

    public function up(Schema $schema): void
    {
        $mappings = [
            ['cadastro.pessoas.view', 'cadastros.pessoas.view', 'Listar pessoas', 'cadastros.pessoas'],
            ['cadastro.pessoas.create', 'cadastros.pessoas.create_edit', 'Criar/editar pessoas', 'cadastros.pessoas'],
            ['cadastro.formas_pagamento.view', 'cadastros.formas_pagamento.view', 'Listar formas de pagamento', 'cadastros.formas_pagamento'],
            ['cadastro.formas_pagamento.create', 'cadastros.formas_pagamento.create_edit', 'Criar/editar formas de pagamento', 'cadastros.formas_pagamento'],
            ['cadastro.contas_financeiras.view', 'cadastros.contas_financeiras.view', 'Listar contas financeiras', 'cadastros.contas_financeiras'],
            ['cadastro.contas_financeiras.create', 'cadastros.contas_financeiras.create_edit', 'Criar/editar contas financeiras', 'cadastros.contas_financeiras'],
            ['cadastro.centros_custo.view', 'cadastros.centros_custo.view', 'Listar centros de custo', 'cadastro'],
            ['cadastro.centros_custo.create', 'cadastros.centros_custo.create_edit', 'Criar/editar centros de custo', 'cadastro'],
            ['cadastro.categorias_financeiras.view', 'cadastros.categorias_financeiras.view', 'Listar categorias financeiras', 'cadastro'],
            ['cadastro.categorias_financeiras.create', 'cadastros.categorias_financeiras.create_edit', 'Criar/editar categorias financeiras', 'cadastro'],
        ];

        foreach ($mappings as [$legacyCode, $newCode, $newName, $moduleCode]) {
            $this->addSql(sprintf(
                "INSERT INTO permissoes (modulo_id, codigo, nome, status)
                 SELECT m.id, '%s', '%s', 'active'
                 FROM modulos m
                 WHERE m.codigo = '%s'
                   AND NOT EXISTS (SELECT 1 FROM permissoes WHERE codigo = '%s')",
                $newCode,
                $newName,
                $moduleCode,
                $newCode
            ));

            $this->addSql(sprintf(
                "INSERT IGNORE INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
                 SELECT pap.perfil_acesso_id, p_new.id
                 FROM perfil_acesso_permissoes pap
                 INNER JOIN permissoes p_old ON p_old.id = pap.permissao_id
                 INNER JOIN permissoes p_new ON p_new.codigo = '%s'
                 WHERE p_old.codigo = '%s'",
                $newCode,
                $legacyCode
            ));
        }

        $this->addSql("
            DELETE pap
            FROM perfil_acesso_permissoes pap
            INNER JOIN permissoes p ON p.id = pap.permissao_id
            WHERE p.codigo IN (
                'cadastro.pessoas.view',
                'cadastro.pessoas.create',
                'cadastro.formas_pagamento.view',
                'cadastro.formas_pagamento.create',
                'cadastro.contas_financeiras.view',
                'cadastro.contas_financeiras.create',
                'cadastro.centros_custo.view',
                'cadastro.centros_custo.create',
                'cadastro.categorias_financeiras.view',
                'cadastro.categorias_financeiras.create'
            )
        ");

        $this->addSql("
            DELETE FROM permissoes
            WHERE codigo IN (
                'cadastro.pessoas.view',
                'cadastro.pessoas.create',
                'cadastro.formas_pagamento.view',
                'cadastro.formas_pagamento.create',
                'cadastro.contas_financeiras.view',
                'cadastro.contas_financeiras.create',
                'cadastro.centros_custo.view',
                'cadastro.centros_custo.create',
                'cadastro.categorias_financeiras.view',
                'cadastro.categorias_financeiras.create'
            )
        ");
    }

    public function down(Schema $schema): void
    {
        $mappings = [
            ['cadastro.pessoas.view', 'cadastros.pessoas.view', 'Listar pessoas'],
            ['cadastro.pessoas.create', 'cadastros.pessoas.create_edit', 'Criar pessoas'],
            ['cadastro.formas_pagamento.view', 'cadastros.formas_pagamento.view', 'Listar formas de pagamento'],
            ['cadastro.formas_pagamento.create', 'cadastros.formas_pagamento.create_edit', 'Criar formas de pagamento'],
            ['cadastro.contas_financeiras.view', 'cadastros.contas_financeiras.view', 'Listar contas financeiras'],
            ['cadastro.contas_financeiras.create', 'cadastros.contas_financeiras.create_edit', 'Criar contas financeiras'],
            ['cadastro.centros_custo.view', 'cadastros.centros_custo.view', 'Listar centros de custo'],
            ['cadastro.centros_custo.create', 'cadastros.centros_custo.create_edit', 'Criar centros de custo'],
            ['cadastro.categorias_financeiras.view', 'cadastros.categorias_financeiras.view', 'Listar categorias financeiras'],
            ['cadastro.categorias_financeiras.create', 'cadastros.categorias_financeiras.create_edit', 'Criar categorias financeiras'],
        ];

        foreach ($mappings as [$legacyCode, $newCode, $legacyName]) {
            $this->addSql(sprintf(
                "INSERT INTO permissoes (modulo_id, codigo, nome, status)
                 SELECT m.id, '%s', '%s', 'active'
                 FROM modulos m
                 WHERE m.codigo = 'cadastro'
                   AND NOT EXISTS (SELECT 1 FROM permissoes WHERE codigo = '%s')",
                $legacyCode,
                $legacyName,
                $legacyCode
            ));

            $this->addSql(sprintf(
                "INSERT IGNORE INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
                 SELECT pap.perfil_acesso_id, p_old.id
                 FROM perfil_acesso_permissoes pap
                 INNER JOIN permissoes p_new ON p_new.id = pap.permissao_id
                 INNER JOIN permissoes p_old ON p_old.codigo = '%s'
                 WHERE p_new.codigo = '%s'",
                $legacyCode,
                $newCode
            ));
        }
    }
}
