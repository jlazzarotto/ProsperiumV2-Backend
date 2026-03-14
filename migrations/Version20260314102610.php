<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314102610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create empresas table with ERP base schema.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "CREATE TABLE empresas (
                id_empresa INT AUTO_INCREMENT NOT NULL,
                razao_social VARCHAR(255) NOT NULL,
                nome_fantasia VARCHAR(255) DEFAULT NULL,
                cnpj VARCHAR(20) DEFAULT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'ativa',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY(id_empresa)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE empresas');
    }
}
