<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315015001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Enrich bancos catalog with metadata from bancos-brasileiros source.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE bancos ADD documento VARCHAR(20) DEFAULT NULL, ADD nome_curto VARCHAR(120) DEFAULT NULL, ADD rede VARCHAR(30) DEFAULT NULL, ADD tipo VARCHAR(120) DEFAULT NULL, ADD tipo_pix VARCHAR(20) DEFAULT NULL, ADD site VARCHAR(255) DEFAULT NULL, ADD data_inicio_operacao DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)', ADD data_inicio_pix DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', ADD data_registro_origem DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', ADD data_atualizacao_origem DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', ADD updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql("UPDATE bancos SET updated_at = created_at WHERE created_at IS NOT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE bancos DROP documento, DROP nome_curto, DROP rede, DROP tipo, DROP tipo_pix, DROP site, DROP data_inicio_operacao, DROP data_inicio_pix, DROP data_registro_origem, DROP data_atualizacao_origem, DROP updated_at");
    }
}
