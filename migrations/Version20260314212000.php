<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314212000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Normalize identity schema indexes to Doctrine naming conventions.';
    }

    public function up(Schema $schema): void
    {
        $this->renameIndexIfExists('permissoes', 'fk_permissoes_modulo', 'IDX_7D2D6A4BC07F55F5');
        $this->renameIndexIfExists('user_perfis_acesso', 'fk_user_perfis_company', 'IDX_8A07D3F979B1AD6');
        $this->renameIndexIfExists('user_perfis_acesso', 'fk_user_perfis_perfil', 'IDX_8A07D3F452AB116');
        $this->renameIndexIfExists('user_perfis_acesso', 'fk_user_perfis_empresa', 'IDX_8A07D3F521E1991');
        $this->renameIndexIfExists('user_perfis_acesso', 'fk_user_perfis_unidade', 'IDX_8A07D3FEDF4B99B');
        $this->renameIndexIfExists('user_companies', 'fk_user_companies_company', 'IDX_82A427DE979B1AD6');
        $this->renameIndexIfExists('perfil_acesso_permissoes', 'fk_pap_permissao', 'IDX_DD0AAF97E009E574');
        $this->renameIndexIfExists('user_unidades', 'fk_user_unidades_company', 'IDX_E41D50C4979B1AD6');
        $this->renameIndexIfExists('user_unidades', 'fk_user_unidades_unidade', 'IDX_E41D50C4EDF4B99B');
        $this->renameIndexIfExists('user_empresas', 'fk_user_empresas_company', 'IDX_9050ADA5979B1AD6');
        $this->renameIndexIfExists('user_empresas', 'fk_user_empresas_empresa', 'IDX_9050ADA5521E1991');
        $this->createIndexIfMissing('user_alcadas', 'IDX_30AACD63979B1AD6', '(company_id)');
        $this->dropIndexIfExists('user_alcadas', 'idx_user_alcadas_lookup');
        $this->renameIndexIfExists('user_alcadas', 'fk_user_alcadas_user', 'IDX_30AACD63A76ED395');
        $this->renameIndexIfExists('user_alcadas', 'fk_user_alcadas_empresa', 'IDX_30AACD63521E1991');
        $this->renameIndexIfExists('user_alcadas', 'fk_user_alcadas_unidade', 'IDX_30AACD63EDF4B99B');
    }

    public function down(Schema $schema): void
    {
        $this->renameIndexIfExists('permissoes', 'IDX_7D2D6A4BC07F55F5', 'fk_permissoes_modulo');
        $this->renameIndexIfExists('user_perfis_acesso', 'IDX_8A07D3F979B1AD6', 'fk_user_perfis_company');
        $this->renameIndexIfExists('user_perfis_acesso', 'IDX_8A07D3F452AB116', 'fk_user_perfis_perfil');
        $this->renameIndexIfExists('user_perfis_acesso', 'IDX_8A07D3F521E1991', 'fk_user_perfis_empresa');
        $this->renameIndexIfExists('user_perfis_acesso', 'IDX_8A07D3FEDF4B99B', 'fk_user_perfis_unidade');
        $this->renameIndexIfExists('user_companies', 'IDX_82A427DE979B1AD6', 'fk_user_companies_company');
        $this->renameIndexIfExists('perfil_acesso_permissoes', 'IDX_DD0AAF97E009E574', 'fk_pap_permissao');
        $this->renameIndexIfExists('user_unidades', 'IDX_E41D50C4979B1AD6', 'fk_user_unidades_company');
        $this->renameIndexIfExists('user_unidades', 'IDX_E41D50C4EDF4B99B', 'fk_user_unidades_unidade');
        $this->renameIndexIfExists('user_empresas', 'IDX_9050ADA5979B1AD6', 'fk_user_empresas_company');
        $this->renameIndexIfExists('user_empresas', 'IDX_9050ADA5521E1991', 'fk_user_empresas_empresa');
        $this->dropIndexIfExists('user_alcadas', 'IDX_30AACD63979B1AD6');
        $this->createIndexIfMissing('user_alcadas', 'idx_user_alcadas_lookup', '(company_id, user_id, empresa_id, unidade_id, tipo_operacao, status)');
        $this->renameIndexIfExists('user_alcadas', 'IDX_30AACD63A76ED395', 'fk_user_alcadas_user');
        $this->renameIndexIfExists('user_alcadas', 'IDX_30AACD63521E1991', 'fk_user_alcadas_empresa');
        $this->renameIndexIfExists('user_alcadas', 'IDX_30AACD63EDF4B99B', 'fk_user_alcadas_unidade');
    }

    private function renameIndexIfExists(string $table, string $from, string $to): void
    {
        if ($this->indexExists($table, $from) && !$this->indexExists($table, $to)) {
            $this->connection->executeStatement(sprintf('ALTER TABLE %s RENAME INDEX %s TO %s', $table, $from, $to));
        }
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if ($this->indexExists($table, $index)) {
            $this->connection->executeStatement(sprintf('DROP INDEX %s ON %s', $index, $table));
        }
    }

    private function createIndexIfMissing(string $table, string $index, string $definition): void
    {
        if (!$this->indexExists($table, $index)) {
            $this->connection->executeStatement(sprintf('CREATE INDEX %s ON %s %s', $index, $table, $definition));
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        return (int) $this->connection->fetchOne(
            'SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?',
            [$table, $index]
        ) > 0;
    }
}
