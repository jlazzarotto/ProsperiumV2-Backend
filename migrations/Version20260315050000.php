<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315050000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria histórico de consultas PSP e renomeia módulo/menu para Porto Sem Papel (PSP).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE psp_consultas_historico (
            id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
            company_id BIGINT UNSIGNED DEFAULT NULL,
            user_id BIGINT UNSIGNED DEFAULT NULL,
            endpoint_key VARCHAR(80) NOT NULL,
            request_json JSON NOT NULL,
            response_json JSON DEFAULT NULL,
            success TINYINT(1) NOT NULL,
            duration_ms INT NOT NULL,
            error_message VARCHAR(500) DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX idx_psp_consultas_lookup (endpoint_key, created_at),
            INDEX idx_psp_consultas_company (company_id, created_at),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql("UPDATE modulos SET nome = 'Porto Sem Papel (PSP)', menu_label = 'Porto Sem Papel (PSP)' WHERE codigo = 'admin.importacao_dados'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE psp_consultas_historico');
        $this->addSql("UPDATE modulos SET nome = 'Importação de Dados', menu_label = 'Importação de Dados' WHERE codigo = 'admin.importacao_dados'");
    }
}
