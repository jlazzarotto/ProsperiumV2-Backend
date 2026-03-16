<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Módulo Pessoas: ajusta tabela pessoas ao schema canônico e cria pessoa_enderecos e pessoa_contatos.';
    }

    public function up(Schema $schema): void
    {
        // 1) Remover FK de empresa antes de dropar a coluna
        $this->addSql('ALTER TABLE pessoas DROP FOREIGN KEY FK_PESSOA_EMPRESA');

        // 2) Ajustar tabela pessoas
        $this->addSql("
            ALTER TABLE pessoas
                CHANGE COLUMN nome       nome_razao    VARCHAR(180) NOT NULL,
                CHANGE COLUMN classificacao tipo_pessoa CHAR(2) NOT NULL,
                ADD COLUMN nome_fantasia    VARCHAR(180)           DEFAULT NULL AFTER nome_razao,
                MODIFY COLUMN documento     VARCHAR(20)            DEFAULT NULL,
                ADD COLUMN inscricao_estadual VARCHAR(30)          DEFAULT NULL AFTER documento,
                ADD COLUMN email_principal    VARCHAR(160)         DEFAULT NULL AFTER inscricao_estadual,
                ADD COLUMN telefone_principal VARCHAR(30)          DEFAULT NULL AFTER email_principal,
                MODIFY COLUMN status          VARCHAR(20)          NOT NULL DEFAULT 'active',
                ADD COLUMN created_by         BIGINT UNSIGNED      DEFAULT NULL AFTER status,
                ADD COLUMN updated_by         BIGINT UNSIGNED      DEFAULT NULL AFTER created_by,
                ADD COLUMN deleted_at         DATETIME             DEFAULT NULL AFTER updated_at,
                DROP COLUMN empresa_id
        ");

        // 3) Índices adicionais
        $this->addSql('ALTER TABLE pessoas ADD UNIQUE INDEX uk_pessoas_company_documento (company_id, documento)');
        $this->addSql('CREATE INDEX idx_pessoas_company_nome ON pessoas (company_id, nome_razao)');

        // 4) Criar tabela pessoa_enderecos
        $this->addSql("
            CREATE TABLE pessoa_enderecos (
                id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id       BIGINT UNSIGNED NOT NULL,
                pessoa_id        BIGINT UNSIGNED NOT NULL,
                tipo_endereco    VARCHAR(20)     NOT NULL,
                logradouro       VARCHAR(180)    NOT NULL,
                numero           VARCHAR(20)     DEFAULT NULL,
                complemento      VARCHAR(120)    DEFAULT NULL,
                bairro           VARCHAR(120)    DEFAULT NULL,
                cidade           VARCHAR(120)    NOT NULL,
                uf               CHAR(2)         DEFAULT NULL,
                cep              VARCHAR(10)     DEFAULT NULL,
                pais             VARCHAR(60)     NOT NULL DEFAULT 'Brasil',
                principal        TINYINT(1)      NOT NULL DEFAULT 0,
                created_at       DATETIME        NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                created_by       BIGINT UNSIGNED DEFAULT NULL,
                updated_at       DATETIME        NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_by       BIGINT UNSIGNED DEFAULT NULL,
                deleted_at       DATETIME        DEFAULT NULL,
                INDEX idx_pessoa_enderecos_lookup (company_id, pessoa_id),
                CONSTRAINT FK_PEND_COMPANY FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
                CONSTRAINT FK_PEND_PESSOA  FOREIGN KEY (pessoa_id)  REFERENCES pessoas   (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ");

        // 5) Criar tabela pessoa_contatos
        $this->addSql("
            CREATE TABLE pessoa_contatos (
                id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id     BIGINT UNSIGNED NOT NULL,
                pessoa_id      BIGINT UNSIGNED NOT NULL,
                nome_contato   VARCHAR(160)    NOT NULL,
                cargo          VARCHAR(80)     DEFAULT NULL,
                email          VARCHAR(160)    DEFAULT NULL,
                telefone       VARCHAR(30)     DEFAULT NULL,
                principal      TINYINT(1)      NOT NULL DEFAULT 0,
                created_at     DATETIME        NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                created_by     BIGINT UNSIGNED DEFAULT NULL,
                updated_at     DATETIME        NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_by     BIGINT UNSIGNED DEFAULT NULL,
                deleted_at     DATETIME        DEFAULT NULL,
                INDEX idx_pessoa_contatos_lookup (company_id, pessoa_id),
                CONSTRAINT FK_PCONT_COMPANY FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE,
                CONSTRAINT FK_PCONT_PESSOA  FOREIGN KEY (pessoa_id)  REFERENCES pessoas   (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ");

        // 6) Permissões adicionais para o módulo pessoas
        $this->addSql("INSERT INTO permissoes (modulo_id, codigo, nome, status) SELECT id, 'cadastros.pessoas.delete', 'Deletar pessoas', 'active' FROM modulos WHERE codigo='cadastro' AND NOT EXISTS (SELECT 1 FROM permissoes WHERE codigo='cadastros.pessoas.delete')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS pessoa_contatos');
        $this->addSql('DROP TABLE IF EXISTS pessoa_enderecos');

        $this->addSql('ALTER TABLE pessoas DROP INDEX uk_pessoas_company_documento');
        $this->addSql('DROP INDEX idx_pessoas_company_nome ON pessoas');

        $this->addSql("
            ALTER TABLE pessoas
                ADD COLUMN empresa_id BIGINT UNSIGNED DEFAULT NULL AFTER company_id,
                CHANGE COLUMN nome_razao    nome          VARCHAR(255) NOT NULL,
                CHANGE COLUMN tipo_pessoa   classificacao VARCHAR(20)  NOT NULL,
                DROP COLUMN nome_fantasia,
                MODIFY COLUMN documento     VARCHAR(40)   DEFAULT NULL,
                DROP COLUMN inscricao_estadual,
                DROP COLUMN email_principal,
                DROP COLUMN telefone_principal,
                MODIFY COLUMN status        VARCHAR(30)   NOT NULL DEFAULT 'active',
                DROP COLUMN created_by,
                DROP COLUMN updated_by,
                DROP COLUMN deleted_at
        ");

        $this->addSql('ALTER TABLE pessoas ADD CONSTRAINT FK_PESSOA_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE SET NULL');
    }
}
