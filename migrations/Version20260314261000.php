<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260314261000 extends AbstractMigration
{
    public function getDescription(): string { return 'Normalize contabil foreign key index names to Doctrine naming conventions.'; }
    public function up(Schema $schema): void
    {
        $this->renameIndexIfExists('dre_mapeamento_categorias', 'fk_dre_map_company', 'IDX_3A0379E4979B1AD6');
        $this->renameIndexIfExists('dre_mapeamento_categorias', 'fk_dre_map_grupo', 'IDX_3A0379E4954BD4D3');
        $this->renameIndexIfExists('dre_mapeamento_categorias', 'fk_dre_map_categoria', 'IDX_3A0379E4495BC94');
        $this->renameIndexIfExists('lancamentos_contabeis_itens', 'fk_lanc_contabil_item_lanc', 'IDX_291A72775DB94A63');
        $this->renameIndexIfExists('lancamentos_contabeis_itens', 'fk_lanc_contabil_item_conta', 'IDX_291A7277DE7172D9');
        $this->renameIndexIfExists('contas_contabeis', 'fk_conta_contabil_parent', 'IDX_6AEA1E7A727ACA70');
        $this->renameIndexIfExists('snapshots_fluxo_caixa', 'fk_snapshot_caixa_empresa', 'IDX_71B51DA3521E1991');
        $this->renameIndexIfExists('snapshots_fluxo_caixa', 'fk_snapshot_caixa_unidade', 'IDX_71B51DA3EDF4B99B');
        $this->renameIndexIfExists('lancamentos_contabeis', 'fk_lanc_contabil_empresa', 'IDX_7157636D521E1991');
        $this->renameIndexIfExists('lancamentos_contabeis', 'fk_lanc_contabil_unidade', 'IDX_7157636DEDF4B99B');
        $this->renameIndexIfExists('lancamentos_contabeis', 'fk_lanc_contabil_titulo', 'IDX_7157636D61AD3496');
        $this->renameIndexIfExists('indicadores_financeiros', 'fk_ind_fin_empresa', 'IDX_7A44EB0D521E1991');
        $this->renameIndexIfExists('indicadores_financeiros', 'fk_ind_fin_unidade', 'IDX_7A44EB0DEDF4B99B');
        $this->renameIndexIfExists('contas_financeiras_saldos_diarios', 'fk_saldo_diario_empresa', 'IDX_909C02B2521E1991');
        $this->renameIndexIfExists('contas_financeiras_saldos_diarios', 'fk_saldo_diario_unidade', 'IDX_909C02B2EDF4B99B');
        $this->renameIndexIfExists('contas_financeiras_saldos_diarios', 'fk_saldo_diario_conta', 'IDX_909C02B232F8769B');
    }
    public function down(Schema $schema): void
    {
        $this->renameIndexIfExists('dre_mapeamento_categorias', 'IDX_3A0379E4979B1AD6', 'fk_dre_map_company');
        $this->renameIndexIfExists('dre_mapeamento_categorias', 'IDX_3A0379E4954BD4D3', 'fk_dre_map_grupo');
        $this->renameIndexIfExists('dre_mapeamento_categorias', 'IDX_3A0379E4495BC94', 'fk_dre_map_categoria');
        $this->renameIndexIfExists('lancamentos_contabeis_itens', 'IDX_291A72775DB94A63', 'fk_lanc_contabil_item_lanc');
        $this->renameIndexIfExists('lancamentos_contabeis_itens', 'IDX_291A7277DE7172D9', 'fk_lanc_contabil_item_conta');
        $this->renameIndexIfExists('contas_contabeis', 'IDX_6AEA1E7A727ACA70', 'fk_conta_contabil_parent');
        $this->renameIndexIfExists('snapshots_fluxo_caixa', 'IDX_71B51DA3521E1991', 'fk_snapshot_caixa_empresa');
        $this->renameIndexIfExists('snapshots_fluxo_caixa', 'IDX_71B51DA3EDF4B99B', 'fk_snapshot_caixa_unidade');
        $this->renameIndexIfExists('lancamentos_contabeis', 'IDX_7157636D521E1991', 'fk_lanc_contabil_empresa');
        $this->renameIndexIfExists('lancamentos_contabeis', 'IDX_7157636DEDF4B99B', 'fk_lanc_contabil_unidade');
        $this->renameIndexIfExists('lancamentos_contabeis', 'IDX_7157636D61AD3496', 'fk_lanc_contabil_titulo');
        $this->renameIndexIfExists('indicadores_financeiros', 'IDX_7A44EB0D521E1991', 'fk_ind_fin_empresa');
        $this->renameIndexIfExists('indicadores_financeiros', 'IDX_7A44EB0DEDF4B99B', 'fk_ind_fin_unidade');
        $this->renameIndexIfExists('contas_financeiras_saldos_diarios', 'IDX_909C02B2521E1991', 'fk_saldo_diario_empresa');
        $this->renameIndexIfExists('contas_financeiras_saldos_diarios', 'IDX_909C02B2EDF4B99B', 'fk_saldo_diario_unidade');
        $this->renameIndexIfExists('contas_financeiras_saldos_diarios', 'IDX_909C02B232F8769B', 'fk_saldo_diario_conta');
    }
    private function renameIndexIfExists(string $table, string $from, string $to): void
    {
        $existsFrom = (int) $this->connection->fetchOne('SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?', [$table, $from]) > 0;
        $existsTo = (int) $this->connection->fetchOne('SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?', [$table, $to]) > 0;
        if ($existsFrom && !$existsTo) { $this->connection->executeStatement(sprintf('ALTER TABLE %s RENAME INDEX %s TO %s', $table, $from, $to)); }
    }
}
