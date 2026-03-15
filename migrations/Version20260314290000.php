<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314290000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Reset application data while preserving bootstrap metadata and ROLE_ROOT users.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');

        $this->addSql('DELETE FROM auditoria_logs');

        $this->addSql('DELETE FROM aprovacoes_titulos_itens');
        $this->addSql('DELETE FROM aprovacoes_titulos');
        $this->addSql('DELETE FROM titulos_comentarios');
        $this->addSql('DELETE FROM tarefas_operacionais_bpo_historico');
        $this->addSql('DELETE FROM tarefas_operacionais_bpo');
        $this->addSql('DELETE FROM regras_automaticas_classificacao');
        $this->addSql('DELETE FROM notificacoes_sistema');

        $this->addSql('DELETE FROM boletos_retorno_itens');
        $this->addSql('DELETE FROM boletos_remessa_itens');
        $this->addSql('DELETE FROM boletos_remessa');
        $this->addSql('DELETE FROM borderos_pagamento_itens');
        $this->addSql('DELETE FROM borderos_pagamento');
        $this->addSql('DELETE FROM borderos_recebimento_itens');
        $this->addSql('DELETE FROM borderos_recebimento');
        $this->addSql('DELETE FROM pix_eventos_webhook');
        $this->addSql('DELETE FROM pix_recebimentos');
        $this->addSql('DELETE FROM pix_cobrancas');

        $this->addSql('DELETE FROM conciliacoes_bancarias');
        $this->addSql('DELETE FROM extratos_bancarios');
        $this->addSql('DELETE FROM conciliacao_regras');
        $this->addSql('DELETE FROM integracoes_bancarias');

        $this->addSql('DELETE FROM lancamentos_contabeis_itens');
        $this->addSql('DELETE FROM lancamentos_contabeis');
        $this->addSql('DELETE FROM dre_mapeamento_categorias');
        $this->addSql('DELETE FROM dre_grupos');
        $this->addSql('DELETE FROM contas_financeiras_saldos_diarios');
        $this->addSql('DELETE FROM indicadores_financeiros');
        $this->addSql('DELETE FROM snapshots_fluxo_caixa');
        $this->addSql('DELETE FROM contas_contabeis');

        $this->addSql('DELETE FROM anexos_financeiros');
        $this->addSql('DELETE FROM baixas');
        $this->addSql('DELETE FROM movimentos_financeiros');
        $this->addSql('DELETE FROM titulos_parcelas');
        $this->addSql('DELETE FROM titulos');

        $this->addSql('DELETE FROM contas_financeiras');
        $this->addSql('DELETE FROM formas_pagamento');
        $this->addSql('DELETE FROM centros_custo');
        $this->addSql('DELETE FROM categorias_financeiras');
        $this->addSql('DELETE FROM pessoas');

        $this->addSql('DELETE FROM user_perfis_acesso');
        $this->addSql('DELETE FROM user_alcadas');
        $this->addSql('DELETE FROM user_unidades');
        $this->addSql('DELETE FROM user_empresas');
        $this->addSql('DELETE FROM user_companies');

        $this->addSql('DELETE pap FROM perfil_acesso_permissoes pap INNER JOIN perfis_acesso p ON p.id = pap.perfil_acesso_id WHERE p.company_id IS NOT NULL');
        $this->addSql('DELETE FROM perfis_acesso WHERE company_id IS NOT NULL');

        $this->addSql('DELETE FROM unidades_negocio');
        $this->addSql('DELETE FROM empresas');
        $this->addSql('DELETE FROM tenant_instances');
        $this->addSql('DELETE FROM companies');

        $this->addSql("DELETE FROM users WHERE role IS NULL OR role <> 'ROLE_ROOT'");

        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('This migration removes runtime data and cannot be reversed.');
    }
}
