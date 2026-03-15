<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314231000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Normalize financeiro foreign key index names to Doctrine naming conventions.';
    }

    public function up(Schema $schema): void
    {
        $this->renameIndexIfExists('titulos_parcelas', 'fk_titpar_tit', 'IDX_386FDE4661AD3496');
        $this->renameIndexIfExists('titulos_parcelas', 'fk_titpar_empresa', 'IDX_386FDE46521E1991');
        $this->renameIndexIfExists('titulos_parcelas', 'fk_titpar_unidade', 'IDX_386FDE46EDF4B99B');
        $this->renameIndexIfExists('anexos_financeiros', 'fk_anexo_company', 'IDX_676CDECC979B1AD6');
        $this->renameIndexIfExists('anexos_financeiros', 'fk_anexo_titulo', 'IDX_676CDECC61AD3496');
        $this->renameIndexIfExists('movimentos_financeiros', 'fk_mov_company', 'IDX_7F0A09E7979B1AD6');
        $this->renameIndexIfExists('movimentos_financeiros', 'fk_mov_empresa', 'IDX_7F0A09E7521E1991');
        $this->renameIndexIfExists('movimentos_financeiros', 'fk_mov_unidade', 'IDX_7F0A09E7EDF4B99B');
        $this->renameIndexIfExists('movimentos_financeiros', 'fk_mov_conta', 'IDX_7F0A09E732F8769B');
        $this->renameIndexIfExists('movimentos_financeiros', 'fk_mov_titulo', 'IDX_7F0A09E761AD3496');
        $this->renameIndexIfExists('movimentos_financeiros', 'fk_mov_baixa', 'IDX_7F0A09E726EE4626');
        $this->renameIndexIfExists('baixas', 'fk_baixa_company', 'IDX_F98AAA0E979B1AD6');
        $this->renameIndexIfExists('baixas', 'fk_baixa_empresa', 'IDX_F98AAA0E521E1991');
        $this->renameIndexIfExists('baixas', 'fk_baixa_unidade', 'IDX_F98AAA0EEDF4B99B');
        $this->renameIndexIfExists('baixas', 'fk_baixa_parcela', 'IDX_F98AAA0EA57188A');
        $this->renameIndexIfExists('baixas', 'fk_baixa_conta', 'IDX_F98AAA0E32F8769B');
        $this->renameIndexIfExists('titulos', 'fk_tit_empresa', 'IDX_90A706DF521E1991');
        $this->renameIndexIfExists('titulos', 'fk_tit_unidade', 'IDX_90A706DFEDF4B99B');
        $this->renameIndexIfExists('titulos', 'fk_tit_pessoa', 'IDX_90A706DFDF6FA0A5');
        $this->renameIndexIfExists('titulos', 'fk_tit_conta', 'IDX_90A706DF32F8769B');
    }

    public function down(Schema $schema): void
    {
        $this->renameIndexIfExists('titulos_parcelas', 'IDX_386FDE4661AD3496', 'fk_titpar_tit');
        $this->renameIndexIfExists('titulos_parcelas', 'IDX_386FDE46521E1991', 'fk_titpar_empresa');
        $this->renameIndexIfExists('titulos_parcelas', 'IDX_386FDE46EDF4B99B', 'fk_titpar_unidade');
        $this->renameIndexIfExists('anexos_financeiros', 'IDX_676CDECC979B1AD6', 'fk_anexo_company');
        $this->renameIndexIfExists('anexos_financeiros', 'IDX_676CDECC61AD3496', 'fk_anexo_titulo');
        $this->renameIndexIfExists('movimentos_financeiros', 'IDX_7F0A09E7979B1AD6', 'fk_mov_company');
        $this->renameIndexIfExists('movimentos_financeiros', 'IDX_7F0A09E7521E1991', 'fk_mov_empresa');
        $this->renameIndexIfExists('movimentos_financeiros', 'IDX_7F0A09E7EDF4B99B', 'fk_mov_unidade');
        $this->renameIndexIfExists('movimentos_financeiros', 'IDX_7F0A09E732F8769B', 'fk_mov_conta');
        $this->renameIndexIfExists('movimentos_financeiros', 'IDX_7F0A09E761AD3496', 'fk_mov_titulo');
        $this->renameIndexIfExists('movimentos_financeiros', 'IDX_7F0A09E726EE4626', 'fk_mov_baixa');
        $this->renameIndexIfExists('baixas', 'IDX_F98AAA0E979B1AD6', 'fk_baixa_company');
        $this->renameIndexIfExists('baixas', 'IDX_F98AAA0E521E1991', 'fk_baixa_empresa');
        $this->renameIndexIfExists('baixas', 'IDX_F98AAA0EEDF4B99B', 'fk_baixa_unidade');
        $this->renameIndexIfExists('baixas', 'IDX_F98AAA0EA57188A', 'fk_baixa_parcela');
        $this->renameIndexIfExists('baixas', 'IDX_F98AAA0E32F8769B', 'fk_baixa_conta');
        $this->renameIndexIfExists('titulos', 'IDX_90A706DF521E1991', 'fk_tit_empresa');
        $this->renameIndexIfExists('titulos', 'IDX_90A706DFEDF4B99B', 'fk_tit_unidade');
        $this->renameIndexIfExists('titulos', 'IDX_90A706DFDF6FA0A5', 'fk_tit_pessoa');
        $this->renameIndexIfExists('titulos', 'IDX_90A706DF32F8769B', 'fk_tit_conta');
    }

    private function renameIndexIfExists(string $table, string $from, string $to): void
    {
        $existsFrom = (int) $this->connection->fetchOne(
            'SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?',
            [$table, $from]
        ) > 0;
        $existsTo = (int) $this->connection->fetchOne(
            'SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?',
            [$table, $to]
        ) > 0;

        if ($existsFrom && !$existsTo) {
            $this->connection->executeStatement(sprintf('ALTER TABLE %s RENAME INDEX %s TO %s', $table, $from, $to));
        }
    }
}
