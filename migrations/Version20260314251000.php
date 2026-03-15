<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260314251000 extends AbstractMigration
{
    public function getDescription(): string { return 'Normalize BPO foreign key index names to Doctrine naming conventions.'; }
    public function up(Schema $schema): void
    {
        $this->renameIndexIfExists('titulos_comentarios', 'fk_tcom_company', 'IDX_EC7ABE76979B1AD6');
        $this->renameIndexIfExists('titulos_comentarios', 'fk_tcom_empresa', 'IDX_EC7ABE76521E1991');
        $this->renameIndexIfExists('titulos_comentarios', 'fk_tcom_unidade', 'IDX_EC7ABE76EDF4B99B');
        $this->renameIndexIfExists('titulos_comentarios', 'fk_tcom_titulo', 'IDX_EC7ABE7661AD3496');
        $this->renameIndexIfExists('titulos_comentarios', 'fk_tcom_user', 'IDX_EC7ABE76A76ED395');
        $this->renameIndexIfExists('aprovacoes_titulos', 'fk_apt_empresa', 'IDX_6743404A521E1991');
        $this->renameIndexIfExists('aprovacoes_titulos', 'fk_apt_unidade', 'IDX_6743404AEDF4B99B');
        $this->renameIndexIfExists('aprovacoes_titulos', 'fk_apt_titulo', 'IDX_6743404A61AD3496');
        $this->renameIndexIfExists('aprovacoes_titulos', 'fk_apt_solicitante', 'IDX_6743404AF761E6FE');
        $this->renameIndexIfExists('tarefas_operacionais_bpo_historico', 'fk_tbpoh_tarefa', 'IDX_30E898A4E4C27B6E');
        $this->renameIndexIfExists('tarefas_operacionais_bpo_historico', 'fk_tbpoh_user', 'IDX_30E898A4A76ED395');
        $this->renameIndexIfExists('notificacoes_sistema', 'fk_ns_empresa', 'IDX_5E44DA09521E1991');
        $this->renameIndexIfExists('notificacoes_sistema', 'fk_ns_unidade', 'IDX_5E44DA09EDF4B99B');
        $this->renameIndexIfExists('notificacoes_sistema', 'fk_ns_user', 'IDX_5E44DA09A76ED395');
        $this->renameIndexIfExists('aprovacoes_titulos_itens', 'fk_apti_aprovacao', 'IDX_72020D2791009ABF');
        $this->renameIndexIfExists('aprovacoes_titulos_itens', 'fk_apti_aprovador', 'IDX_72020D27970FB1A0');
        $this->renameIndexIfExists('tarefas_operacionais_bpo', 'fk_tbpo_empresa', 'IDX_865A6139521E1991');
        $this->renameIndexIfExists('tarefas_operacionais_bpo', 'fk_tbpo_unidade', 'IDX_865A6139EDF4B99B');
        $this->renameIndexIfExists('tarefas_operacionais_bpo', 'fk_tbpo_titulo', 'IDX_865A613961AD3496');
        $this->renameIndexIfExists('tarefas_operacionais_bpo', 'fk_tbpo_resp', 'IDX_865A6139F53845BE');
        $this->renameIndexIfExists('regras_automaticas_classificacao', 'fk_rac_empresa', 'IDX_6A009042521E1991');
        $this->renameIndexIfExists('regras_automaticas_classificacao', 'fk_rac_unidade', 'IDX_6A009042EDF4B99B');
        $this->renameIndexIfExists('regras_automaticas_classificacao', 'fk_rac_categoria', 'IDX_6A009042495BC94');
        $this->renameIndexIfExists('regras_automaticas_classificacao', 'fk_rac_centro', 'IDX_6A009042EF6B01F7');
    }
    public function down(Schema $schema): void
    {
        $this->renameIndexIfExists('titulos_comentarios', 'IDX_EC7ABE76979B1AD6', 'fk_tcom_company');
        $this->renameIndexIfExists('titulos_comentarios', 'IDX_EC7ABE76521E1991', 'fk_tcom_empresa');
        $this->renameIndexIfExists('titulos_comentarios', 'IDX_EC7ABE76EDF4B99B', 'fk_tcom_unidade');
        $this->renameIndexIfExists('titulos_comentarios', 'IDX_EC7ABE7661AD3496', 'fk_tcom_titulo');
        $this->renameIndexIfExists('titulos_comentarios', 'IDX_EC7ABE76A76ED395', 'fk_tcom_user');
        $this->renameIndexIfExists('aprovacoes_titulos', 'IDX_6743404A521E1991', 'fk_apt_empresa');
        $this->renameIndexIfExists('aprovacoes_titulos', 'IDX_6743404AEDF4B99B', 'fk_apt_unidade');
        $this->renameIndexIfExists('aprovacoes_titulos', 'IDX_6743404A61AD3496', 'fk_apt_titulo');
        $this->renameIndexIfExists('aprovacoes_titulos', 'IDX_6743404AF761E6FE', 'fk_apt_solicitante');
        $this->renameIndexIfExists('tarefas_operacionais_bpo_historico', 'IDX_30E898A4E4C27B6E', 'fk_tbpoh_tarefa');
        $this->renameIndexIfExists('tarefas_operacionais_bpo_historico', 'IDX_30E898A4A76ED395', 'fk_tbpoh_user');
        $this->renameIndexIfExists('notificacoes_sistema', 'IDX_5E44DA09521E1991', 'fk_ns_empresa');
        $this->renameIndexIfExists('notificacoes_sistema', 'IDX_5E44DA09EDF4B99B', 'fk_ns_unidade');
        $this->renameIndexIfExists('notificacoes_sistema', 'IDX_5E44DA09A76ED395', 'fk_ns_user');
        $this->renameIndexIfExists('aprovacoes_titulos_itens', 'IDX_72020D2791009ABF', 'fk_apti_aprovacao');
        $this->renameIndexIfExists('aprovacoes_titulos_itens', 'IDX_72020D27970FB1A0', 'fk_apti_aprovador');
        $this->renameIndexIfExists('tarefas_operacionais_bpo', 'IDX_865A6139521E1991', 'fk_tbpo_empresa');
        $this->renameIndexIfExists('tarefas_operacionais_bpo', 'IDX_865A6139EDF4B99B', 'fk_tbpo_unidade');
        $this->renameIndexIfExists('tarefas_operacionais_bpo', 'IDX_865A613961AD3496', 'fk_tbpo_titulo');
        $this->renameIndexIfExists('tarefas_operacionais_bpo', 'IDX_865A6139F53845BE', 'fk_tbpo_resp');
        $this->renameIndexIfExists('regras_automaticas_classificacao', 'IDX_6A009042521E1991', 'fk_rac_empresa');
        $this->renameIndexIfExists('regras_automaticas_classificacao', 'IDX_6A009042EDF4B99B', 'fk_rac_unidade');
        $this->renameIndexIfExists('regras_automaticas_classificacao', 'IDX_6A009042495BC94', 'fk_rac_categoria');
        $this->renameIndexIfExists('regras_automaticas_classificacao', 'IDX_6A009042EF6B01F7', 'fk_rac_centro');
    }
    private function renameIndexIfExists(string $table, string $from, string $to): void
    {
        $existsFrom = (int) $this->connection->fetchOne('SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?', [$table, $from]) > 0;
        $existsTo = (int) $this->connection->fetchOne('SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?', [$table, $to]) > 0;
        if ($existsFrom && !$existsTo) { $this->connection->executeStatement(sprintf('ALTER TABLE %s RENAME INDEX %s TO %s', $table, $from, $to)); }
    }
}
