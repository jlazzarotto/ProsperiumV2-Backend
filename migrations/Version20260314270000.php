<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314270000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Enrich audit trail and seed standard access profiles.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $columns = $schemaManager->listTableColumns('auditoria_logs');
        $foreignKeys = $schemaManager->listTableForeignKeys('auditoria_logs');
        $indexes = $schemaManager->listTableIndexes('auditoria_logs');

        if (!isset($columns['empresa_id'])) {
            $this->addSql('ALTER TABLE auditoria_logs ADD empresa_id BIGINT UNSIGNED DEFAULT NULL');
        }

        if (!isset($columns['unidade_id'])) {
            $this->addSql('ALTER TABLE auditoria_logs ADD unidade_id BIGINT UNSIGNED DEFAULT NULL');
        }

        if (!isset($columns['user_id'])) {
            $this->addSql('ALTER TABLE auditoria_logs ADD user_id BIGINT UNSIGNED DEFAULT NULL');
        }

        if (!isset($columns['request_id'])) {
            $this->addSql('ALTER TABLE auditoria_logs ADD request_id VARCHAR(64) DEFAULT NULL');
        }

        if (!isset($columns['request_path'])) {
            $this->addSql('ALTER TABLE auditoria_logs ADD request_path VARCHAR(255) DEFAULT NULL');
        }

        if (!isset($columns['request_method'])) {
            $this->addSql('ALTER TABLE auditoria_logs ADD request_method VARCHAR(10) DEFAULT NULL');
        }

        if (!isset($columns['ip_address'])) {
            $this->addSql('ALTER TABLE auditoria_logs ADD ip_address VARCHAR(64) DEFAULT NULL');
        }

        if (!$this->hasForeignKey($foreignKeys, 'fk_auditoria_logs_empresa')) {
            $this->addSql('ALTER TABLE auditoria_logs ADD CONSTRAINT fk_auditoria_logs_empresa FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE SET NULL');
        }

        if (!$this->hasForeignKey($foreignKeys, 'fk_auditoria_logs_unidade')) {
            $this->addSql('ALTER TABLE auditoria_logs ADD CONSTRAINT fk_auditoria_logs_unidade FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE SET NULL');
        }

        if (!$this->hasForeignKey($foreignKeys, 'fk_auditoria_logs_user')) {
            $this->addSql('ALTER TABLE auditoria_logs ADD CONSTRAINT fk_auditoria_logs_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        }

        if (!$this->hasIndex($indexes, 'idx_auditoria_logs_empresa')) {
            $this->addSql('CREATE INDEX idx_auditoria_logs_empresa ON auditoria_logs (empresa_id)');
        }

        if (!$this->hasIndex($indexes, 'idx_auditoria_logs_unidade')) {
            $this->addSql('CREATE INDEX idx_auditoria_logs_unidade ON auditoria_logs (unidade_id)');
        }

        if (!$this->hasIndex($indexes, 'idx_auditoria_logs_user')) {
            $this->addSql('CREATE INDEX idx_auditoria_logs_user ON auditoria_logs (user_id)');
        }

        $this->addSql("INSERT INTO perfis_acesso (company_id, codigo, nome, tipo, status)
            SELECT NULL, 'gestor_financeiro', 'Gestor Financeiro', 'system', 'active'
            WHERE NOT EXISTS (SELECT 1 FROM perfis_acesso WHERE codigo = 'gestor_financeiro')");
        $this->addSql("INSERT INTO perfis_acesso (company_id, codigo, nome, tipo, status)
            SELECT NULL, 'operador_financeiro', 'Operador Financeiro', 'system', 'active'
            WHERE NOT EXISTS (SELECT 1 FROM perfis_acesso WHERE codigo = 'operador_financeiro')");
        $this->addSql("INSERT INTO perfis_acesso (company_id, codigo, nome, tipo, status)
            SELECT NULL, 'analista_bpo', 'Analista BPO', 'system', 'active'
            WHERE NOT EXISTS (SELECT 1 FROM perfis_acesso WHERE codigo = 'analista_bpo')");
        $this->addSql("INSERT INTO perfis_acesso (company_id, codigo, nome, tipo, status)
            SELECT NULL, 'auditor_consulta', 'Auditor/Consulta', 'system', 'active'
            WHERE NOT EXISTS (SELECT 1 FROM perfis_acesso WHERE codigo = 'auditor_consulta')");
        $this->addSql("INSERT INTO perfis_acesso (company_id, codigo, nome, tipo, status)
            SELECT NULL, 'aprovador', 'Aprovador', 'system', 'active'
            WHERE NOT EXISTS (SELECT 1 FROM perfis_acesso WHERE codigo = 'aprovador')");

        $this->grantProfilePermissions('gestor_financeiro', [
            'cadastro.%',
            'financeiro.%',
            'tesouraria.%',
            'cobranca.%',
            'contabil.%',
            'bpo.tarefas.%',
            'bpo.titulos.comentarios.create',
            'bpo.aprovacoes.create',
            'bpo.regras_automaticas.create',
            'identity.permissoes.view',
        ]);

        $this->grantProfilePermissions('operador_financeiro', [
            'cadastro.%',
            'financeiro.titulos.%',
            'financeiro.parcelas.baixar',
            'tesouraria.extratos.%',
            'tesouraria.conciliacoes.%',
            'cobranca.boletos.%',
            'cobranca.pix.cobrancas.%',
            'bpo.tarefas.%',
            'bpo.titulos.comentarios.create',
        ]);

        $this->grantProfilePermissions('analista_bpo', [
            'cadastro.pessoas.%',
            'financeiro.titulos.view',
            'tesouraria.conciliacoes.view',
            'bpo.%',
        ]);

        $this->grantProfilePermissions('auditor_consulta', [
            'identity.users.view',
            'identity.permissoes.view',
            'financeiro.titulos.view',
            'tesouraria.extratos.view',
            'tesouraria.conciliacoes.view',
            'cobranca.pix.cobrancas.view',
            'contabil.contas_contabeis.view',
            'contabil.dre.view',
            'contabil.indicadores.view',
            'bpo.tarefas.view',
        ]);

        $this->grantProfilePermissions('aprovador', [
            'financeiro.titulos.view',
            'bpo.aprovacoes.create',
            'bpo.tarefas.view',
            'bpo.titulos.comentarios.create',
        ]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE pap FROM perfil_acesso_permissoes pap JOIN perfis_acesso p ON p.id = pap.perfil_acesso_id WHERE p.codigo IN ('gestor_financeiro', 'operador_financeiro', 'analista_bpo', 'auditor_consulta', 'aprovador')");
        $this->addSql("DELETE FROM perfis_acesso WHERE codigo IN ('gestor_financeiro', 'operador_financeiro', 'analista_bpo', 'auditor_consulta', 'aprovador')");

        $this->addSql('ALTER TABLE auditoria_logs DROP FOREIGN KEY fk_auditoria_logs_empresa');
        $this->addSql('ALTER TABLE auditoria_logs DROP FOREIGN KEY fk_auditoria_logs_unidade');
        $this->addSql('ALTER TABLE auditoria_logs DROP FOREIGN KEY fk_auditoria_logs_user');
        $this->addSql('DROP INDEX idx_auditoria_logs_empresa ON auditoria_logs');
        $this->addSql('DROP INDEX idx_auditoria_logs_unidade ON auditoria_logs');
        $this->addSql('DROP INDEX idx_auditoria_logs_user ON auditoria_logs');
        $this->addSql('ALTER TABLE auditoria_logs DROP COLUMN empresa_id, DROP COLUMN unidade_id, DROP COLUMN user_id, DROP COLUMN request_id, DROP COLUMN request_path, DROP COLUMN request_method, DROP COLUMN ip_address');
    }

    /**
     * @param list<string> $permissionPatterns
     */
    private function grantProfilePermissions(string $profileCode, array $permissionPatterns): void
    {
        foreach ($permissionPatterns as $pattern) {
            $operator = str_contains($pattern, '%') ? 'LIKE' : '=';

            $this->addSql(sprintf(
                "INSERT INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id)
                SELECT p.id, perm.id
                FROM perfis_acesso p
                JOIN permissoes perm ON perm.codigo %s '%s'
                WHERE p.codigo = '%s'
                  AND NOT EXISTS (
                    SELECT 1
                    FROM perfil_acesso_permissoes x
                    WHERE x.perfil_acesso_id = p.id
                      AND x.permissao_id = perm.id
                )",
                $operator,
                $pattern,
                $profileCode
            ));
        }
    }

    /**
     * @param list<\Doctrine\DBAL\Schema\ForeignKeyConstraint> $foreignKeys
     */
    private function hasForeignKey(array $foreignKeys, string $name): bool
    {
        foreach ($foreignKeys as $foreignKey) {
            if (strtolower($foreignKey->getName()) === strtolower($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, \Doctrine\DBAL\Schema\Index> $indexes
     */
    private function hasIndex(array $indexes, string $name): bool
    {
        return isset($indexes[$name]) || isset($indexes[strtolower($name)]) || isset($indexes[strtoupper($name)]);
    }
}
