<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314221000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Normalize cadastro foreign key index names to Doctrine naming conventions.';
    }

    public function up(Schema $schema): void
    {
        $this->renameIndexIfExists('categorias_financeiras', 'fk_cat_fin_parent', 'IDX_C1A87DF1727ACA70');
        $this->renameIndexIfExists('centros_custo', 'fk_cc_parent', 'IDX_44E01107727ACA70');
        $this->renameIndexIfExists('pessoas', 'fk_pessoa_empresa', 'IDX_18A4F2AC521E1991');
        $this->renameIndexIfExists('contas_financeiras', 'fk_conta_fin_empresa', 'IDX_AF28845B521E1991');
        $this->renameIndexIfExists('contas_financeiras', 'fk_conta_fin_unidade', 'IDX_AF28845BEDF4B99B');
    }

    public function down(Schema $schema): void
    {
        $this->renameIndexIfExists('categorias_financeiras', 'IDX_C1A87DF1727ACA70', 'fk_cat_fin_parent');
        $this->renameIndexIfExists('centros_custo', 'IDX_44E01107727ACA70', 'fk_cc_parent');
        $this->renameIndexIfExists('pessoas', 'IDX_18A4F2AC521E1991', 'fk_pessoa_empresa');
        $this->renameIndexIfExists('contas_financeiras', 'IDX_AF28845B521E1991', 'fk_conta_fin_empresa');
        $this->renameIndexIfExists('contas_financeiras', 'IDX_AF28845BEDF4B99B', 'fk_conta_fin_unidade');
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
