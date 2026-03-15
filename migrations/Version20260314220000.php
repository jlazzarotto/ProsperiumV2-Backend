<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260314220000 extends AbstractMigration
{
    public function getDescription(): string { return 'Create cadastro base tables and seed cadastro permissions.'; }
    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE pessoas (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, company_id BIGINT UNSIGNED NOT NULL, empresa_id BIGINT UNSIGNED DEFAULT NULL, nome VARCHAR(255) NOT NULL, documento VARCHAR(40) DEFAULT NULL, classificacao VARCHAR(20) NOT NULL, status VARCHAR(30) NOT NULL DEFAULT 'active', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX idx_pessoas_company (company_id, status), CONSTRAINT FK_PESSOA_COMPANY FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE, CONSTRAINT FK_PESSOA_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE SET NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE categorias_financeiras (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, company_id BIGINT UNSIGNED NOT NULL, parent_id BIGINT UNSIGNED DEFAULT NULL, codigo VARCHAR(50) NOT NULL, nome VARCHAR(255) NOT NULL, tipo VARCHAR(20) NOT NULL, status VARCHAR(30) NOT NULL DEFAULT 'active', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX idx_categorias_company (company_id, status), CONSTRAINT FK_CAT_FIN_COMPANY FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE, CONSTRAINT FK_CAT_FIN_PARENT FOREIGN KEY (parent_id) REFERENCES categorias_financeiras (id) ON DELETE SET NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE centros_custo (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, company_id BIGINT UNSIGNED NOT NULL, parent_id BIGINT UNSIGNED DEFAULT NULL, codigo VARCHAR(50) NOT NULL, nome VARCHAR(255) NOT NULL, status VARCHAR(30) NOT NULL DEFAULT 'active', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX idx_centros_custo_company (company_id, status), CONSTRAINT FK_CC_COMPANY FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE, CONSTRAINT FK_CC_PARENT FOREIGN KEY (parent_id) REFERENCES centros_custo (id) ON DELETE SET NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE contas_financeiras (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, company_id BIGINT UNSIGNED NOT NULL, empresa_id BIGINT UNSIGNED NOT NULL, unidade_id BIGINT UNSIGNED DEFAULT NULL, codigo VARCHAR(50) NOT NULL, nome VARCHAR(255) NOT NULL, tipo VARCHAR(50) NOT NULL, status VARCHAR(30) NOT NULL DEFAULT 'active', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE KEY uk_contas_financeiras (company_id, empresa_id, codigo), INDEX idx_contas_financeiras_lookup (company_id, empresa_id, unidade_id, status), CONSTRAINT FK_CONTA_FIN_COMPANY FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE, CONSTRAINT FK_CONTA_FIN_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE, CONSTRAINT FK_CONTA_FIN_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE SET NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE formas_pagamento (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, company_id BIGINT UNSIGNED NOT NULL, codigo VARCHAR(50) NOT NULL, nome VARCHAR(255) NOT NULL, tipo VARCHAR(50) NOT NULL, status VARCHAR(30) NOT NULL DEFAULT 'active', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX idx_formas_pagamento_company (company_id, status), CONSTRAINT FK_FORMA_PGTO_COMPANY FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("INSERT INTO modulos (codigo, nome, status) SELECT 'cadastro', 'Cadastros Base', 'active' WHERE NOT EXISTS (SELECT 1 FROM modulos WHERE codigo='cadastro')");
        foreach ([
            ['cadastros.pessoas.view','Listar pessoas'],
            ['cadastros.pessoas.create_edit','Criar/editar pessoas'],
            ['cadastros.categorias_financeiras.view','Listar categorias financeiras'],
            ['cadastros.categorias_financeiras.create_edit','Criar/editar categorias financeiras'],
            ['cadastros.centros_custo.view','Listar centros de custo'],
            ['cadastros.centros_custo.create_edit','Criar/editar centros de custo'],
            ['cadastros.contas_financeiras.view','Listar contas financeiras'],
            ['cadastros.contas_financeiras.create_edit','Criar/editar contas financeiras'],
            ['cadastros.formas_pagamento.view','Listar formas de pagamento'],
            ['cadastros.formas_pagamento.create_edit','Criar/editar formas de pagamento'],
        ] as [$codigo,$nome]) {
            $this->addSql("INSERT INTO permissoes (modulo_id, codigo, nome, status) SELECT id, '$codigo', '$nome', 'active' FROM modulos WHERE codigo='cadastro' AND NOT EXISTS (SELECT 1 FROM permissoes WHERE codigo='$codigo')");
        }
        $this->addSql("INSERT INTO perfil_acesso_permissoes (perfil_acesso_id, permissao_id) SELECT p.id, perm.id FROM perfis_acesso p JOIN permissoes perm ON perm.codigo LIKE 'cadastros.%' WHERE p.codigo='company_admin' AND NOT EXISTS (SELECT 1 FROM perfil_acesso_permissoes x WHERE x.perfil_acesso_id=p.id AND x.permissao_id=perm.id)");
    }
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS formas_pagamento');
        $this->addSql('DROP TABLE IF EXISTS contas_financeiras');
        $this->addSql('DROP TABLE IF EXISTS centros_custo');
        $this->addSql('DROP TABLE IF EXISTS categorias_financeiras');
        $this->addSql('DROP TABLE IF EXISTS pessoas');
    }
}
