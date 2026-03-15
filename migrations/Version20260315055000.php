<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315055000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Alinha permissões legadas de cadastro.* para o namespace cadastros.* usado pelo frontend.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            INSERT IGNORE INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
            SELECT pap.perfil_acesso_id, p_new.id
            FROM perfil_acesso_permissoes pap
            INNER JOIN permissoes p_old ON p_old.id = pap.permissao_id
            INNER JOIN permissoes p_new ON p_new.codigo = 'cadastros.pessoas.view'
            WHERE p_old.codigo = 'cadastro.pessoas.view'
        ");
        $this->addSql("
            INSERT IGNORE INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
            SELECT pap.perfil_acesso_id, p_new.id
            FROM perfil_acesso_permissoes pap
            INNER JOIN permissoes p_old ON p_old.id = pap.permissao_id
            INNER JOIN permissoes p_new ON p_new.codigo = 'cadastros.pessoas.create_edit'
            WHERE p_old.codigo = 'cadastro.pessoas.create'
        ");
        $this->addSql("
            INSERT IGNORE INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
            SELECT pap.perfil_acesso_id, p_new.id
            FROM perfil_acesso_permissoes pap
            INNER JOIN permissoes p_old ON p_old.id = pap.permissao_id
            INNER JOIN permissoes p_new ON p_new.codigo = 'cadastros.formas_pagamento.view'
            WHERE p_old.codigo = 'cadastro.formas_pagamento.view'
        ");
        $this->addSql("
            INSERT IGNORE INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
            SELECT pap.perfil_acesso_id, p_new.id
            FROM perfil_acesso_permissoes pap
            INNER JOIN permissoes p_old ON p_old.id = pap.permissao_id
            INNER JOIN permissoes p_new ON p_new.codigo = 'cadastros.formas_pagamento.create_edit'
            WHERE p_old.codigo = 'cadastro.formas_pagamento.create'
        ");
        $this->addSql("
            INSERT IGNORE INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
            SELECT pap.perfil_acesso_id, p_new.id
            FROM perfil_acesso_permissoes pap
            INNER JOIN permissoes p_old ON p_old.id = pap.permissao_id
            INNER JOIN permissoes p_new ON p_new.codigo = 'cadastros.contas_financeiras.view'
            WHERE p_old.codigo = 'cadastro.contas_financeiras.view'
        ");
        $this->addSql("
            INSERT IGNORE INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
            SELECT pap.perfil_acesso_id, p_new.id
            FROM perfil_acesso_permissoes pap
            INNER JOIN permissoes p_old ON p_old.id = pap.permissao_id
            INNER JOIN permissoes p_new ON p_new.codigo = 'cadastros.contas_financeiras.create_edit'
            WHERE p_old.codigo = 'cadastro.contas_financeiras.create'
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            DELETE pap
            FROM perfil_acesso_permissoes pap
            INNER JOIN permissoes p ON p.id = pap.permissao_id
            WHERE p.codigo IN (
                'cadastros.pessoas.view',
                'cadastros.pessoas.create_edit',
                'cadastros.formas_pagamento.view',
                'cadastros.formas_pagamento.create_edit',
                'cadastros.contas_financeiras.view',
                'cadastros.contas_financeiras.create_edit'
            )
        ");
    }
}
