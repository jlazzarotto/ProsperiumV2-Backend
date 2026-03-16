<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315057000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Expande tabela empresas: apelido, abreviatura, documentos fiscais, endereço, soft delete e cnpj opcional.';
    }

    public function up(Schema $schema): void
    {
        // Cria índice standalone em company_id antes de dropar o unique (que é o único índice desta coluna usado pela FK)
        $this->addSql('CREATE INDEX idx_empresas_company ON empresas (company_id)');
        $this->addSql('DROP INDEX uk_empresas_company_cnpj ON empresas');

        $this->addSql("
            ALTER TABLE empresas
                MODIFY COLUMN cnpj VARCHAR(20) DEFAULT NULL,
                ADD COLUMN apelido VARCHAR(255) DEFAULT NULL AFTER nome_fantasia,
                ADD COLUMN abreviatura VARCHAR(50) DEFAULT NULL AFTER apelido,
                ADD COLUMN inscricao_estadual VARCHAR(50) DEFAULT NULL AFTER abreviatura,
                ADD COLUMN inscricao_municipal VARCHAR(50) DEFAULT NULL AFTER inscricao_estadual,
                ADD COLUMN cep VARCHAR(9) DEFAULT NULL AFTER inscricao_municipal,
                ADD COLUMN estado VARCHAR(50) DEFAULT NULL AFTER cep,
                ADD COLUMN cidade VARCHAR(100) DEFAULT NULL AFTER estado,
                ADD COLUMN logradouro VARCHAR(255) DEFAULT NULL AFTER cidade,
                ADD COLUMN numero VARCHAR(20) DEFAULT NULL AFTER logradouro,
                ADD COLUMN complemento VARCHAR(100) DEFAULT NULL AFTER numero,
                ADD COLUMN bairro VARCHAR(100) DEFAULT NULL AFTER complemento,
                ADD COLUMN deleted_at DATETIME DEFAULT NULL AFTER updated_at
        ");

        $this->addSql('CREATE UNIQUE INDEX uk_empresas_company_cnpj ON empresas (company_id, cnpj)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uk_empresas_company_cnpj ON empresas');

        $this->addSql("
            ALTER TABLE empresas
                DROP COLUMN deleted_at,
                DROP COLUMN bairro,
                DROP COLUMN complemento,
                DROP COLUMN numero,
                DROP COLUMN logradouro,
                DROP COLUMN cidade,
                DROP COLUMN estado,
                DROP COLUMN cep,
                DROP COLUMN inscricao_municipal,
                DROP COLUMN inscricao_estadual,
                DROP COLUMN abreviatura,
                DROP COLUMN apelido,
                MODIFY COLUMN cnpj VARCHAR(20) NOT NULL
        ");

        $this->addSql('CREATE UNIQUE INDEX uk_empresas_company_cnpj ON empresas (company_id, cnpj)');
        $this->addSql('DROP INDEX idx_empresas_company ON empresas');
    }
}
