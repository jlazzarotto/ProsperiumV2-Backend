<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314234000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Normalize tesouraria foreign key index names to Doctrine naming conventions.';
    }

    public function up(Schema $schema): void
    {
        $this->renameIndexIfExists('extratos_bancarios', 'fk_ext_empresa', 'IDX_6FB3C357521E1991');
        $this->renameIndexIfExists('extratos_bancarios', 'fk_ext_unidade', 'IDX_6FB3C357EDF4B99B');
        $this->renameIndexIfExists('extratos_bancarios', 'fk_ext_conta', 'IDX_6FB3C35732F8769B');
        $this->renameIndexIfExists('extratos_bancarios', 'fk_ext_mov', 'IDX_6FB3C3571ABEF188');
        $this->renameIndexIfExists('extratos_bancarios', 'fk_ext_baixa', 'IDX_6FB3C35726EE4626');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'fk_conc_company', 'IDX_EF4B724979B1AD6');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'fk_conc_empresa', 'IDX_EF4B724521E1991');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'fk_conc_unidade', 'IDX_EF4B724EDF4B99B');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'fk_conc_ext', 'IDX_EF4B72488334B21');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'fk_conc_mov', 'IDX_EF4B7241ABEF188');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'fk_conc_baixa', 'IDX_EF4B72426EE4626');
        $this->renameIndexIfExists('integracoes_bancarias', 'fk_intb_company', 'IDX_26BE12BF979B1AD6');
        $this->renameIndexIfExists('integracoes_bancarias', 'fk_intb_empresa', 'IDX_26BE12BF521E1991');
        $this->renameIndexIfExists('integracoes_bancarias', 'fk_intb_unidade', 'IDX_26BE12BFEDF4B99B');
        $this->renameIndexIfExists('integracoes_bancarias', 'fk_intb_conta', 'IDX_26BE12BF32F8769B');
        $this->renameIndexIfExists('conciliacao_regras', 'fk_regras_company', 'IDX_F91ABA71979B1AD6');
        $this->renameIndexIfExists('conciliacao_regras', 'fk_regras_empresa', 'IDX_F91ABA71521E1991');
        $this->renameIndexIfExists('conciliacao_regras', 'fk_regras_unidade', 'IDX_F91ABA71EDF4B99B');
        $this->renameIndexIfExists('conciliacao_regras', 'fk_regras_conta', 'IDX_F91ABA7132F8769B');
    }

    public function down(Schema $schema): void
    {
        $this->renameIndexIfExists('extratos_bancarios', 'IDX_6FB3C357521E1991', 'fk_ext_empresa');
        $this->renameIndexIfExists('extratos_bancarios', 'IDX_6FB3C357EDF4B99B', 'fk_ext_unidade');
        $this->renameIndexIfExists('extratos_bancarios', 'IDX_6FB3C35732F8769B', 'fk_ext_conta');
        $this->renameIndexIfExists('extratos_bancarios', 'IDX_6FB3C3571ABEF188', 'fk_ext_mov');
        $this->renameIndexIfExists('extratos_bancarios', 'IDX_6FB3C35726EE4626', 'fk_ext_baixa');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'IDX_EF4B724979B1AD6', 'fk_conc_company');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'IDX_EF4B724521E1991', 'fk_conc_empresa');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'IDX_EF4B724EDF4B99B', 'fk_conc_unidade');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'IDX_EF4B72488334B21', 'fk_conc_ext');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'IDX_EF4B7241ABEF188', 'fk_conc_mov');
        $this->renameIndexIfExists('conciliacoes_bancarias', 'IDX_EF4B72426EE4626', 'fk_conc_baixa');
        $this->renameIndexIfExists('integracoes_bancarias', 'IDX_26BE12BF979B1AD6', 'fk_intb_company');
        $this->renameIndexIfExists('integracoes_bancarias', 'IDX_26BE12BF521E1991', 'fk_intb_empresa');
        $this->renameIndexIfExists('integracoes_bancarias', 'IDX_26BE12BFEDF4B99B', 'fk_intb_unidade');
        $this->renameIndexIfExists('integracoes_bancarias', 'IDX_26BE12BF32F8769B', 'fk_intb_conta');
        $this->renameIndexIfExists('conciliacao_regras', 'IDX_F91ABA71979B1AD6', 'fk_regras_company');
        $this->renameIndexIfExists('conciliacao_regras', 'IDX_F91ABA71521E1991', 'fk_regras_empresa');
        $this->renameIndexIfExists('conciliacao_regras', 'IDX_F91ABA71EDF4B99B', 'fk_regras_unidade');
        $this->renameIndexIfExists('conciliacao_regras', 'IDX_F91ABA7132F8769B', 'fk_regras_conta');
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
