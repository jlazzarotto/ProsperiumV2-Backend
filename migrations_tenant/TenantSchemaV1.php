<?php

declare(strict_types=1);

namespace DoctrineMigrationsTenant;

use Doctrine\DBAL\Connection;

/**
 * Schema completo do tenant plane (bancos operacionais).
 *
 * Executado via comando app:tenant:provision contra cada banco tenant.
 *
 * Diferenças em relação ao control plane:
 * - companies NÃO existe neste banco; company_id é coluna informacional sem FK
 * - paises, ufs, municipios vivem no control plane (não replicados aqui)
 * - perfis_acesso e perfil_acesso_permissoes vivem aqui (por tenant)
 * - FKs para users são removidas (users vivem no control plane)
 * - Colunas user_id permanecem como referência informacional
 */
final class TenantSchemaV1
{
    public function getVersion(): string
    {
        return 'v1';
    }

    public function getDescription(): string
    {
        return 'Tenant plane — schema completo para bancos operacionais (ERP)';
    }

    /**
     * @return list<string>
     */
    public function up(): array
    {
        return [
            // ============================
            // 1. empresas
            // ============================
            "CREATE TABLE IF NOT EXISTS empresas (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                razao_social VARCHAR(255) NOT NULL,
                nome_fantasia VARCHAR(255) DEFAULT NULL,
                apelido VARCHAR(255) DEFAULT NULL,
                abreviatura VARCHAR(50) DEFAULT NULL,
                cnpj VARCHAR(20) DEFAULT NULL,
                inscricao_estadual VARCHAR(50) DEFAULT NULL,
                inscricao_municipal VARCHAR(50) DEFAULT NULL,
                cep VARCHAR(9) DEFAULT NULL,
                estado VARCHAR(50) DEFAULT NULL,
                cidade VARCHAR(100) DEFAULT NULL,
                logradouro VARCHAR(255) DEFAULT NULL,
                numero VARCHAR(20) DEFAULT NULL,
                complemento VARCHAR(100) DEFAULT NULL,
                bairro VARCHAR(100) DEFAULT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                deleted_at DATETIME DEFAULT NULL,
                UNIQUE KEY uk_empresas_company_cnpj (company_id, cnpj),
                INDEX idx_empresas_company (company_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 3. unidades_negocio
            // ============================
            "CREATE TABLE IF NOT EXISTS unidades_negocio (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                nome VARCHAR(255) NOT NULL,
                abreviatura VARCHAR(50) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                UNIQUE KEY uk_unidades_negocio_company_nome (company_id, nome),
                UNIQUE KEY uk_unidades_negocio_company_abrev (company_id, abreviatura)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 4. pessoas
            // ============================
            "CREATE TABLE IF NOT EXISTS pessoas (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                nome_razao VARCHAR(180) NOT NULL,
                nome_fantasia VARCHAR(180) DEFAULT NULL,
                tipo_pessoa CHAR(2) NOT NULL,
                classificacao VARCHAR(3) DEFAULT NULL,
                documento VARCHAR(20) DEFAULT NULL,
                inscricao_estadual VARCHAR(30) DEFAULT NULL,
                email_principal VARCHAR(160) DEFAULT NULL,
                telefone_principal VARCHAR(30) DEFAULT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'active',
                created_by BIGINT UNSIGNED DEFAULT NULL,
                updated_by BIGINT UNSIGNED DEFAULT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                deleted_at DATETIME DEFAULT NULL,
                UNIQUE KEY uk_pessoas_company_documento (company_id, documento),
                UNIQUE KEY uk_pessoas_company_email (company_id, email_principal),
                INDEX idx_pessoas_company (company_id, status),
                INDEX idx_pessoas_company_nome (company_id, nome_razao),
                INDEX idx_pessoas_classificacao (company_id, classificacao)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 5. pessoa_enderecos
            // ============================
            "CREATE TABLE IF NOT EXISTS pessoa_enderecos (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                pessoa_id BIGINT UNSIGNED NOT NULL,
                tipo_endereco VARCHAR(20) NOT NULL,
                logradouro VARCHAR(180) NOT NULL,
                numero VARCHAR(20) DEFAULT NULL,
                complemento VARCHAR(120) DEFAULT NULL,
                bairro VARCHAR(120) DEFAULT NULL,
                cidade VARCHAR(120) NOT NULL,
                uf CHAR(2) DEFAULT NULL,
                cep VARCHAR(10) DEFAULT NULL,
                pais VARCHAR(60) NOT NULL DEFAULT 'Brasil',
                principal TINYINT(1) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                created_by BIGINT UNSIGNED DEFAULT NULL,
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_by BIGINT UNSIGNED DEFAULT NULL,
                deleted_at DATETIME DEFAULT NULL,
                INDEX idx_pessoa_enderecos_lookup (company_id, pessoa_id),
                CONSTRAINT FK_PEND_PESSOA FOREIGN KEY (pessoa_id) REFERENCES pessoas (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 6. pessoa_contatos
            // ============================
            "CREATE TABLE IF NOT EXISTS pessoa_contatos (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                pessoa_id BIGINT UNSIGNED NOT NULL,
                nome_contato VARCHAR(160) NOT NULL,
                cargo VARCHAR(80) DEFAULT NULL,
                email VARCHAR(160) DEFAULT NULL,
                telefone VARCHAR(30) DEFAULT NULL,
                principal TINYINT(1) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                created_by BIGINT UNSIGNED DEFAULT NULL,
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_by BIGINT UNSIGNED DEFAULT NULL,
                deleted_at DATETIME DEFAULT NULL,
                INDEX idx_pessoa_contatos_lookup (company_id, pessoa_id),
                CONSTRAINT FK_PCONT_PESSOA FOREIGN KEY (pessoa_id) REFERENCES pessoas (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 7. bancos
            // ============================
            "CREATE TABLE IF NOT EXISTS bancos (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                codigo_compe VARCHAR(10) NOT NULL,
                nome VARCHAR(180) NOT NULL,
                ispb VARCHAR(10) DEFAULT NULL,
                documento VARCHAR(20) DEFAULT NULL,
                nome_curto VARCHAR(120) DEFAULT NULL,
                rede VARCHAR(30) DEFAULT NULL,
                tipo VARCHAR(120) DEFAULT NULL,
                tipo_pix VARCHAR(20) DEFAULT NULL,
                site VARCHAR(255) DEFAULT NULL,
                data_inicio_operacao DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
                data_inicio_pix DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                data_registro_origem DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                data_atualizacao_origem DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                status VARCHAR(20) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)',
                UNIQUE KEY uk_bancos_codigo_compe (codigo_compe)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 8. categorias_financeiras
            // ============================
            "CREATE TABLE IF NOT EXISTS categorias_financeiras (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                parent_id BIGINT UNSIGNED DEFAULT NULL,
                codigo VARCHAR(50) NOT NULL,
                nome VARCHAR(255) NOT NULL,
                tipo VARCHAR(20) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_categorias_company (company_id, status),
                CONSTRAINT FK_CAT_FIN_PARENT FOREIGN KEY (parent_id) REFERENCES categorias_financeiras (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 12. centros_custo
            // ============================
            "CREATE TABLE IF NOT EXISTS centros_custo (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                parent_id BIGINT UNSIGNED DEFAULT NULL,
                codigo VARCHAR(50) NOT NULL,
                nome VARCHAR(255) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_centros_custo_company (company_id, status),
                CONSTRAINT FK_CC_PARENT FOREIGN KEY (parent_id) REFERENCES centros_custo (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 13. contas_financeiras
            // ============================
            "CREATE TABLE IF NOT EXISTS contas_financeiras (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED DEFAULT NULL,
                codigo VARCHAR(50) NOT NULL,
                nome VARCHAR(255) NOT NULL,
                tipo VARCHAR(50) NOT NULL,
                banco_id BIGINT UNSIGNED DEFAULT NULL,
                agencia VARCHAR(20) DEFAULT NULL,
                conta_numero VARCHAR(30) DEFAULT NULL,
                conta_digito VARCHAR(5) DEFAULT NULL,
                titular_pessoa_id BIGINT UNSIGNED DEFAULT NULL,
                saldo_inicial DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                data_saldo_inicial DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
                permite_movimento_negativo TINYINT(1) NOT NULL DEFAULT 0,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                UNIQUE KEY uk_contas_financeiras (company_id, empresa_id, codigo),
                INDEX idx_contas_financeiras_lookup (company_id, empresa_id, unidade_id, status),
                INDEX idx_contas_financeiras_banco (banco_id),
                INDEX idx_contas_financeiras_titular (titular_pessoa_id),
                CONSTRAINT FK_CONTA_FIN_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_CONTA_FIN_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE SET NULL,
                CONSTRAINT FK_CONTA_FIN_BANCO FOREIGN KEY (banco_id) REFERENCES bancos (id) ON DELETE SET NULL,
                CONSTRAINT FK_CONTA_FIN_TITULAR FOREIGN KEY (titular_pessoa_id) REFERENCES pessoas (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 14. formas_pagamento
            // ============================
            "CREATE TABLE IF NOT EXISTS formas_pagamento (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                codigo VARCHAR(50) NOT NULL,
                nome VARCHAR(255) NOT NULL,
                tipo VARCHAR(50) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_formas_pagamento_company (company_id, status)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 15. titulos
            // ============================
            "CREATE TABLE IF NOT EXISTS titulos (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                pessoa_id BIGINT UNSIGNED NOT NULL,
                conta_financeira_id BIGINT UNSIGNED DEFAULT NULL,
                tipo VARCHAR(20) NOT NULL,
                numero_documento VARCHAR(100) DEFAULT NULL,
                valor_total DECIMAL(18,2) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'aberto',
                data_emissao DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
                observacoes LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_titulos_lookup (company_id, empresa_id, unidade_id, tipo, status),
                CONSTRAINT FK_TIT_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_TIT_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_TIT_PESSOA FOREIGN KEY (pessoa_id) REFERENCES pessoas (id) ON DELETE CASCADE,
                CONSTRAINT FK_TIT_CONTA FOREIGN KEY (conta_financeira_id) REFERENCES contas_financeiras (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 16. titulos_parcelas
            // ============================
            "CREATE TABLE IF NOT EXISTS titulos_parcelas (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                titulo_id BIGINT UNSIGNED NOT NULL,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                numero INT NOT NULL,
                valor DECIMAL(18,2) NOT NULL,
                valor_aberto DECIMAL(18,2) NOT NULL,
                vencimento DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
                status VARCHAR(30) NOT NULL DEFAULT 'aberto',
                INDEX idx_titulos_parcelas_lookup (company_id, empresa_id, unidade_id, vencimento, status),
                CONSTRAINT FK_TITPAR_TIT FOREIGN KEY (titulo_id) REFERENCES titulos (id) ON DELETE CASCADE,
                CONSTRAINT FK_TITPAR_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_TITPAR_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 17. baixas
            // ============================
            "CREATE TABLE IF NOT EXISTS baixas (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                titulo_parcela_id BIGINT UNSIGNED NOT NULL,
                conta_financeira_id BIGINT UNSIGNED NOT NULL,
                valor DECIMAL(18,2) NOT NULL,
                data_pagamento DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
                observacoes LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                CONSTRAINT FK_BAIXA_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_BAIXA_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_BAIXA_PARCELA FOREIGN KEY (titulo_parcela_id) REFERENCES titulos_parcelas (id) ON DELETE CASCADE,
                CONSTRAINT FK_BAIXA_CONTA FOREIGN KEY (conta_financeira_id) REFERENCES contas_financeiras (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 18. movimentos_financeiros
            // ============================
            "CREATE TABLE IF NOT EXISTS movimentos_financeiros (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                conta_financeira_id BIGINT UNSIGNED NOT NULL,
                titulo_id BIGINT UNSIGNED DEFAULT NULL,
                baixa_id BIGINT UNSIGNED DEFAULT NULL,
                tipo VARCHAR(20) NOT NULL,
                valor DECIMAL(18,2) NOT NULL,
                data_movimento DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
                historico LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                CONSTRAINT FK_MOV_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_MOV_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_MOV_CONTA FOREIGN KEY (conta_financeira_id) REFERENCES contas_financeiras (id) ON DELETE CASCADE,
                CONSTRAINT FK_MOV_TITULO FOREIGN KEY (titulo_id) REFERENCES titulos (id) ON DELETE SET NULL,
                CONSTRAINT FK_MOV_BAIXA FOREIGN KEY (baixa_id) REFERENCES baixas (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 19. anexos_financeiros
            // ============================
            "CREATE TABLE IF NOT EXISTS anexos_financeiros (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                titulo_id BIGINT UNSIGNED NOT NULL,
                file_name VARCHAR(255) NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                mime_type VARCHAR(100) DEFAULT NULL,
                uploaded_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                CONSTRAINT FK_ANEXO_TITULO FOREIGN KEY (titulo_id) REFERENCES titulos (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 20. extratos_bancarios
            // ============================
            "CREATE TABLE IF NOT EXISTS extratos_bancarios (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                conta_financeira_id BIGINT UNSIGNED NOT NULL,
                codigo_externo VARCHAR(100) DEFAULT NULL,
                data_movimento DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
                valor DECIMAL(18,2) NOT NULL,
                tipo VARCHAR(20) NOT NULL,
                descricao VARCHAR(255) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'pendente',
                movimento_financeiro_id BIGINT UNSIGNED DEFAULT NULL,
                baixa_id BIGINT UNSIGNED DEFAULT NULL,
                importado_em DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_extratos_bancarios_lookup (company_id, empresa_id, unidade_id, conta_financeira_id, data_movimento),
                CONSTRAINT FK_EXT_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_EXT_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_EXT_CONTA FOREIGN KEY (conta_financeira_id) REFERENCES contas_financeiras (id) ON DELETE CASCADE,
                CONSTRAINT FK_EXT_MOV FOREIGN KEY (movimento_financeiro_id) REFERENCES movimentos_financeiros (id) ON DELETE SET NULL,
                CONSTRAINT FK_EXT_BAIXA FOREIGN KEY (baixa_id) REFERENCES baixas (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 21. conciliacoes_bancarias
            // ============================
            "CREATE TABLE IF NOT EXISTS conciliacoes_bancarias (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                extrato_bancario_id BIGINT UNSIGNED NOT NULL,
                movimento_financeiro_id BIGINT UNSIGNED DEFAULT NULL,
                baixa_id BIGINT UNSIGNED DEFAULT NULL,
                modo VARCHAR(20) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'confirmada',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                CONSTRAINT FK_CONC_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_CONC_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_CONC_EXT FOREIGN KEY (extrato_bancario_id) REFERENCES extratos_bancarios (id) ON DELETE CASCADE,
                CONSTRAINT FK_CONC_MOV FOREIGN KEY (movimento_financeiro_id) REFERENCES movimentos_financeiros (id) ON DELETE SET NULL,
                CONSTRAINT FK_CONC_BAIXA FOREIGN KEY (baixa_id) REFERENCES baixas (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 22. conciliacao_regras
            // ============================
            "CREATE TABLE IF NOT EXISTS conciliacao_regras (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED DEFAULT NULL,
                unidade_id BIGINT UNSIGNED DEFAULT NULL,
                conta_financeira_id BIGINT UNSIGNED DEFAULT NULL,
                descricao_contains VARCHAR(255) NOT NULL,
                tipo_movimento_sugerido VARCHAR(20) NOT NULL,
                aplicacao VARCHAR(20) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                CONSTRAINT FK_REGRAS_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE SET NULL,
                CONSTRAINT FK_REGRAS_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE SET NULL,
                CONSTRAINT FK_REGRAS_CONTA FOREIGN KEY (conta_financeira_id) REFERENCES contas_financeiras (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 23. integracoes_bancarias
            // ============================
            "CREATE TABLE IF NOT EXISTS integracoes_bancarias (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED DEFAULT NULL,
                unidade_id BIGINT UNSIGNED DEFAULT NULL,
                conta_financeira_id BIGINT UNSIGNED NOT NULL,
                banco VARCHAR(100) NOT NULL,
                integration_type VARCHAR(50) NOT NULL,
                config_json JSON NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                CONSTRAINT FK_INTB_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE SET NULL,
                CONSTRAINT FK_INTB_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE SET NULL,
                CONSTRAINT FK_INTB_CONTA FOREIGN KEY (conta_financeira_id) REFERENCES contas_financeiras (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 24. borderos_pagamento
            // ============================
            "CREATE TABLE IF NOT EXISTS borderos_pagamento (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                conta_financeira_id BIGINT UNSIGNED NOT NULL,
                referencia VARCHAR(100) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'rascunho',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                CONSTRAINT FK_BPG_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_BPG_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_BPG_CONTA FOREIGN KEY (conta_financeira_id) REFERENCES contas_financeiras (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 25. borderos_pagamento_itens
            // ============================
            "CREATE TABLE IF NOT EXISTS borderos_pagamento_itens (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                bordero_pagamento_id BIGINT UNSIGNED NOT NULL,
                titulo_parcela_id BIGINT UNSIGNED NOT NULL,
                valor DECIMAL(18,2) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'pendente',
                CONSTRAINT FK_BPGI_BORDERO FOREIGN KEY (bordero_pagamento_id) REFERENCES borderos_pagamento (id) ON DELETE CASCADE,
                CONSTRAINT FK_BPGI_PARCELA FOREIGN KEY (titulo_parcela_id) REFERENCES titulos_parcelas (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 26. borderos_recebimento
            // ============================
            "CREATE TABLE IF NOT EXISTS borderos_recebimento (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                conta_financeira_id BIGINT UNSIGNED NOT NULL,
                referencia VARCHAR(100) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'rascunho',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                CONSTRAINT FK_BRC_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_BRC_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_BRC_CONTA FOREIGN KEY (conta_financeira_id) REFERENCES contas_financeiras (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 27. borderos_recebimento_itens
            // ============================
            "CREATE TABLE IF NOT EXISTS borderos_recebimento_itens (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                bordero_recebimento_id BIGINT UNSIGNED NOT NULL,
                titulo_parcela_id BIGINT UNSIGNED NOT NULL,
                valor DECIMAL(18,2) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'pendente',
                CONSTRAINT FK_BRCI_BORDERO FOREIGN KEY (bordero_recebimento_id) REFERENCES borderos_recebimento (id) ON DELETE CASCADE,
                CONSTRAINT FK_BRCI_PARCELA FOREIGN KEY (titulo_parcela_id) REFERENCES titulos_parcelas (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 28. boletos_remessa
            // ============================
            "CREATE TABLE IF NOT EXISTS boletos_remessa (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                conta_financeira_id BIGINT UNSIGNED NOT NULL,
                codigo_remessa VARCHAR(100) NOT NULL,
                banco VARCHAR(100) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'gerada',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                UNIQUE KEY uk_boletos_remessa_codigo (company_id, codigo_remessa),
                KEY idx_boletos_remessa_lookup (company_id, empresa_id, unidade_id, status),
                CONSTRAINT FK_BREM_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_BREM_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_BREM_CONTA FOREIGN KEY (conta_financeira_id) REFERENCES contas_financeiras (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 29. boletos_remessa_itens
            // ============================
            "CREATE TABLE IF NOT EXISTS boletos_remessa_itens (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                boleto_remessa_id BIGINT UNSIGNED NOT NULL,
                titulo_parcela_id BIGINT UNSIGNED NOT NULL,
                nosso_numero VARCHAR(100) NOT NULL,
                valor DECIMAL(18,2) NOT NULL,
                vencimento DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
                status VARCHAR(30) NOT NULL DEFAULT 'pendente_registro',
                ocorrencia_retorno VARCHAR(50) DEFAULT NULL,
                data_ocorrencia DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
                UNIQUE KEY uk_boletos_remessa_itens_nosso_numero (nosso_numero),
                CONSTRAINT FK_BRI_REMESSA FOREIGN KEY (boleto_remessa_id) REFERENCES boletos_remessa (id) ON DELETE CASCADE,
                CONSTRAINT FK_BRI_PARCELA FOREIGN KEY (titulo_parcela_id) REFERENCES titulos_parcelas (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 30. boletos_retorno_itens
            // ============================
            "CREATE TABLE IF NOT EXISTS boletos_retorno_itens (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                boleto_remessa_item_id BIGINT UNSIGNED DEFAULT NULL,
                nosso_numero VARCHAR(100) NOT NULL,
                codigo_ocorrencia VARCHAR(50) NOT NULL,
                descricao VARCHAR(255) DEFAULT NULL,
                valor_recebido DECIMAL(18,2) NOT NULL,
                data_ocorrencia DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
                linha_original LONGTEXT DEFAULT NULL,
                importado_em DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                KEY idx_boletos_retorno_lookup (company_id, empresa_id, unidade_id, nosso_numero),
                CONSTRAINT FK_BRET_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_BRET_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_BRET_ITEM FOREIGN KEY (boleto_remessa_item_id) REFERENCES boletos_remessa_itens (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 31. pix_cobrancas
            // ============================
            "CREATE TABLE IF NOT EXISTS pix_cobrancas (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                titulo_parcela_id BIGINT UNSIGNED NOT NULL,
                conta_financeira_id BIGINT UNSIGNED NOT NULL,
                txid VARCHAR(100) NOT NULL,
                chave_pix VARCHAR(255) NOT NULL,
                valor DECIMAL(18,2) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'pendente',
                expiracao_segundos INT NOT NULL,
                calendario_gerado_em DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                calendario_expira_em DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                qr_code LONGTEXT DEFAULT NULL,
                copia_cola LONGTEXT DEFAULT NULL,
                UNIQUE KEY uk_pix_cobrancas_txid (txid),
                KEY idx_pix_cobrancas_lookup (company_id, empresa_id, unidade_id, status),
                CONSTRAINT FK_PIXC_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_PIXC_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_PIXC_PARCELA FOREIGN KEY (titulo_parcela_id) REFERENCES titulos_parcelas (id) ON DELETE CASCADE,
                CONSTRAINT FK_PIXC_CONTA FOREIGN KEY (conta_financeira_id) REFERENCES contas_financeiras (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 32. pix_recebimentos
            // ============================
            "CREATE TABLE IF NOT EXISTS pix_recebimentos (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                pix_cobranca_id BIGINT UNSIGNED NOT NULL,
                end_to_end_id VARCHAR(120) NOT NULL,
                txid VARCHAR(100) NOT NULL,
                valor DECIMAL(18,2) NOT NULL,
                payload_json JSON NOT NULL,
                recebido_em DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                UNIQUE KEY uk_pix_recebimentos_e2e (end_to_end_id),
                CONSTRAINT FK_PIXR_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_PIXR_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_PIXR_COBRANCA FOREIGN KEY (pix_cobranca_id) REFERENCES pix_cobrancas (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 33. pix_eventos_webhook
            // ============================
            "CREATE TABLE IF NOT EXISTS pix_eventos_webhook (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED DEFAULT NULL,
                unidade_id BIGINT UNSIGNED DEFAULT NULL,
                tipo_evento VARCHAR(100) NOT NULL,
                identificador_externo VARCHAR(120) DEFAULT NULL,
                payload_json JSON NOT NULL,
                recebido_em DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                KEY idx_pix_eventos_webhook_lookup (company_id, tipo_evento, identificador_externo),
                CONSTRAINT FK_PIXW_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE SET NULL,
                CONSTRAINT FK_PIXW_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 34. tarefas_operacionais_bpo (sem FK para users — user vive no control plane)
            // ============================
            "CREATE TABLE IF NOT EXISTS tarefas_operacionais_bpo (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                titulo_id BIGINT UNSIGNED DEFAULT NULL,
                responsavel_user_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'Ref informacional a users no control plane',
                tipo VARCHAR(100) NOT NULL,
                descricao LONGTEXT NOT NULL,
                prioridade VARCHAR(20) NOT NULL DEFAULT 'media',
                status VARCHAR(30) NOT NULL DEFAULT 'aberta',
                prazo_em DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                KEY idx_tarefas_bpo_lookup (company_id, empresa_id, unidade_id, status, prioridade),
                INDEX idx_tarefas_bpo_user (responsavel_user_id),
                CONSTRAINT FK_TBPO_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_TBPO_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_TBPO_TITULO FOREIGN KEY (titulo_id) REFERENCES titulos (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 35. tarefas_operacionais_bpo_historico (sem FK para users)
            // ============================
            "CREATE TABLE IF NOT EXISTS tarefas_operacionais_bpo_historico (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                tarefa_operacional_bpo_id BIGINT UNSIGNED NOT NULL,
                user_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'Ref informacional a users no control plane',
                acao VARCHAR(100) NOT NULL,
                observacao LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_tarefas_bpo_hist_user (user_id),
                CONSTRAINT FK_TBPOH_TAREFA FOREIGN KEY (tarefa_operacional_bpo_id) REFERENCES tarefas_operacionais_bpo (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 36. titulos_comentarios (sem FK para users)
            // ============================
            "CREATE TABLE IF NOT EXISTS titulos_comentarios (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                titulo_id BIGINT UNSIGNED NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL COMMENT 'Ref informacional a users no control plane',
                comentario LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_titulos_comentarios_user (user_id),
                CONSTRAINT FK_TCOM_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_TCOM_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_TCOM_TITULO FOREIGN KEY (titulo_id) REFERENCES titulos (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 37. aprovacoes_titulos (sem FK para users)
            // ============================
            "CREATE TABLE IF NOT EXISTS aprovacoes_titulos (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                titulo_id BIGINT UNSIGNED NOT NULL,
                solicitante_user_id BIGINT UNSIGNED NOT NULL COMMENT 'Ref informacional a users no control plane',
                tipo_operacao VARCHAR(100) NOT NULL,
                valor_total DECIMAL(18,2) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'pendente',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                KEY idx_aprovacoes_titulos_lookup (company_id, empresa_id, unidade_id, status),
                INDEX idx_aprovacoes_titulos_user (solicitante_user_id),
                CONSTRAINT FK_APT_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_APT_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_APT_TITULO FOREIGN KEY (titulo_id) REFERENCES titulos (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 38. aprovacoes_titulos_itens (sem FK para users)
            // ============================
            "CREATE TABLE IF NOT EXISTS aprovacoes_titulos_itens (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                aprovacao_titulo_id BIGINT UNSIGNED NOT NULL,
                aprovador_user_id BIGINT UNSIGNED NOT NULL COMMENT 'Ref informacional a users no control plane',
                ordem INT NOT NULL,
                limite_alcada DECIMAL(18,2) DEFAULT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'pendente',
                observacao LONGTEXT DEFAULT NULL,
                decidido_em DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_aprovacoes_itens_user (aprovador_user_id),
                CONSTRAINT FK_APTI_APROVACAO FOREIGN KEY (aprovacao_titulo_id) REFERENCES aprovacoes_titulos (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 39. regras_automaticas_classificacao
            // ============================
            "CREATE TABLE IF NOT EXISTS regras_automaticas_classificacao (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED DEFAULT NULL,
                unidade_id BIGINT UNSIGNED DEFAULT NULL,
                categoria_financeira_id BIGINT UNSIGNED DEFAULT NULL,
                centro_custo_id BIGINT UNSIGNED DEFAULT NULL,
                descricao_contains VARCHAR(255) NOT NULL,
                acao_notificacao TINYINT(1) NOT NULL DEFAULT 1,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                KEY idx_regras_auto_classificacao_lookup (company_id, empresa_id, unidade_id, status),
                CONSTRAINT FK_RAC_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE SET NULL,
                CONSTRAINT FK_RAC_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE SET NULL,
                CONSTRAINT FK_RAC_CATEGORIA FOREIGN KEY (categoria_financeira_id) REFERENCES categorias_financeiras (id) ON DELETE SET NULL,
                CONSTRAINT FK_RAC_CENTRO FOREIGN KEY (centro_custo_id) REFERENCES centros_custo (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 40. notificacoes_sistema (sem FK para users)
            // ============================
            "CREATE TABLE IF NOT EXISTS notificacoes_sistema (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED DEFAULT NULL,
                unidade_id BIGINT UNSIGNED DEFAULT NULL,
                user_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'Ref informacional a users no control plane',
                tipo VARCHAR(100) NOT NULL,
                titulo VARCHAR(255) NOT NULL,
                mensagem LONGTEXT NOT NULL,
                metadata_json JSON NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'pending',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                KEY idx_notificacoes_sistema_lookup (company_id, empresa_id, unidade_id, status),
                INDEX idx_notificacoes_sistema_user (user_id),
                CONSTRAINT FK_NS_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE SET NULL,
                CONSTRAINT FK_NS_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 41. contas_contabeis
            // ============================
            "CREATE TABLE IF NOT EXISTS contas_contabeis (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                parent_id BIGINT UNSIGNED DEFAULT NULL,
                codigo VARCHAR(50) NOT NULL,
                nome VARCHAR(255) NOT NULL,
                tipo VARCHAR(30) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                UNIQUE KEY uk_contas_contabeis_company_codigo (company_id, codigo),
                KEY idx_contas_contabeis_lookup (company_id, tipo, status),
                CONSTRAINT FK_CONTA_CONTABIL_PARENT FOREIGN KEY (parent_id) REFERENCES contas_contabeis (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 42. lancamentos_contabeis
            // ============================
            "CREATE TABLE IF NOT EXISTS lancamentos_contabeis (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                titulo_id BIGINT UNSIGNED DEFAULT NULL,
                data_lancamento DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
                historico LONGTEXT NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'posted',
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                KEY idx_lancamentos_contabeis_lookup (company_id, empresa_id, unidade_id, data_lancamento, status),
                CONSTRAINT FK_LANC_CONTABIL_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_LANC_CONTABIL_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_LANC_CONTABIL_TITULO FOREIGN KEY (titulo_id) REFERENCES titulos (id) ON DELETE SET NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 43. lancamentos_contabeis_itens
            // ============================
            "CREATE TABLE IF NOT EXISTS lancamentos_contabeis_itens (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                lancamento_contabil_id BIGINT UNSIGNED NOT NULL,
                conta_contabil_id BIGINT UNSIGNED NOT NULL,
                natureza VARCHAR(10) NOT NULL,
                valor DECIMAL(18,2) NOT NULL,
                CONSTRAINT FK_LANC_CONTABIL_ITEM_LANC FOREIGN KEY (lancamento_contabil_id) REFERENCES lancamentos_contabeis (id) ON DELETE CASCADE,
                CONSTRAINT FK_LANC_CONTABIL_ITEM_CONTA FOREIGN KEY (conta_contabil_id) REFERENCES contas_contabeis (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 44. dre_grupos
            // ============================
            "CREATE TABLE IF NOT EXISTS dre_grupos (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                codigo VARCHAR(50) NOT NULL,
                nome VARCHAR(255) NOT NULL,
                ordem INT NOT NULL,
                tipo_demonstracao VARCHAR(30) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                KEY idx_dre_grupos_lookup (company_id, ordem, status)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 45. dre_mapeamento_categorias
            // ============================
            "CREATE TABLE IF NOT EXISTS dre_mapeamento_categorias (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                dre_grupo_id BIGINT UNSIGNED NOT NULL,
                categoria_financeira_id BIGINT UNSIGNED NOT NULL,
                CONSTRAINT FK_DRE_MAP_GRUPO FOREIGN KEY (dre_grupo_id) REFERENCES dre_grupos (id) ON DELETE CASCADE,
                CONSTRAINT FK_DRE_MAP_CATEGORIA FOREIGN KEY (categoria_financeira_id) REFERENCES categorias_financeiras (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 46. contas_financeiras_saldos_diarios
            // ============================
            "CREATE TABLE IF NOT EXISTS contas_financeiras_saldos_diarios (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                conta_financeira_id BIGINT UNSIGNED NOT NULL,
                data_saldo DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
                saldo DECIMAL(18,2) NOT NULL,
                UNIQUE KEY uk_cfsd_contexto_data (company_id, empresa_id, unidade_id, conta_financeira_id, data_saldo),
                CONSTRAINT FK_SALDO_DIARIO_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_SALDO_DIARIO_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE,
                CONSTRAINT FK_SALDO_DIARIO_CONTA FOREIGN KEY (conta_financeira_id) REFERENCES contas_financeiras (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 47. indicadores_financeiros
            // ============================
            "CREATE TABLE IF NOT EXISTS indicadores_financeiros (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                codigo VARCHAR(100) NOT NULL,
                nome VARCHAR(255) NOT NULL,
                data_referencia DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
                valor DECIMAL(18,4) NOT NULL,
                metadata_json JSON NOT NULL,
                KEY idx_indicadores_financeiros_lookup (company_id, empresa_id, unidade_id, codigo, data_referencia),
                CONSTRAINT FK_IND_FIN_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_IND_FIN_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 48. snapshots_fluxo_caixa
            // ============================
            "CREATE TABLE IF NOT EXISTS snapshots_fluxo_caixa (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                empresa_id BIGINT UNSIGNED NOT NULL,
                unidade_id BIGINT UNSIGNED NOT NULL,
                data_referencia DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
                saldo_inicial DECIMAL(18,2) NOT NULL,
                entradas_periodo DECIMAL(18,2) NOT NULL,
                saidas_periodo DECIMAL(18,2) NOT NULL,
                saldo_final DECIMAL(18,2) NOT NULL,
                UNIQUE KEY uk_snapshots_fluxo_caixa_contexto_data (company_id, empresa_id, unidade_id, data_referencia),
                CONSTRAINT FK_SNAPSHOT_CAIXA_EMPRESA FOREIGN KEY (empresa_id) REFERENCES empresas (id) ON DELETE CASCADE,
                CONSTRAINT FK_SNAPSHOT_CAIXA_UNIDADE FOREIGN KEY (unidade_id) REFERENCES unidades_negocio (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 49. psp_consultas_historico (sem FK para users)
            // ============================
            "CREATE TABLE IF NOT EXISTS psp_consultas_historico (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED DEFAULT NULL,
                user_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'Ref informacional a users no control plane',
                endpoint_key VARCHAR(80) NOT NULL,
                request_json JSON NOT NULL,
                response_json JSON DEFAULT NULL,
                success TINYINT(1) NOT NULL,
                duration_ms INT NOT NULL,
                error_message VARCHAR(500) DEFAULT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_psp_consultas_lookup (endpoint_key, created_at),
                INDEX idx_psp_consultas_company (company_id, created_at)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 50. config_params
            // ============================
            "CREATE TABLE IF NOT EXISTS config_params (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                type VARCHAR(100) DEFAULT NULL,
                value TEXT NOT NULL,
                description TEXT DEFAULT NULL,
                status SMALLINT NOT NULL DEFAULT 1,
                `restrict` SMALLINT NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_config_params_company_status (company_id, status),
                UNIQUE KEY uk_config_params_company_name (company_id, name)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 51. perfis_acesso
            // ============================
            "CREATE TABLE IF NOT EXISTS perfis_acesso (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED DEFAULT NULL,
                codigo VARCHAR(100) NOT NULL,
                nome VARCHAR(255) NOT NULL,
                tipo VARCHAR(20) NOT NULL DEFAULT 'custom',
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                UNIQUE KEY uk_perfis_acesso_company_codigo (company_id, codigo)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // 52. perfil_acesso_permissoes
            // ============================
            "CREATE TABLE IF NOT EXISTS perfil_acesso_permissoes (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                perfil_acesso_id BIGINT UNSIGNED NOT NULL,
                permissao_id BIGINT UNSIGNED NOT NULL,
                UNIQUE KEY uk_pap (perfil_acesso_id, permissao_id),
                CONSTRAINT fk_pap_perfil FOREIGN KEY (perfil_acesso_id) REFERENCES perfis_acesso (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",

            // ============================
            // Tabela de controle de versão do schema tenant
            // ============================
            "CREATE TABLE IF NOT EXISTS tenant_schema_versions (
                version VARCHAR(50) NOT NULL PRIMARY KEY,
                description VARCHAR(255) NOT NULL,
                applied_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",
        ];
    }

    /**
     * @return list<string>
     */
    public function down(): array
    {
        return [
            'DROP TABLE IF EXISTS tenant_schema_versions',
            'DROP TABLE IF EXISTS config_params',
            'DROP TABLE IF EXISTS psp_consultas_historico',
            'DROP TABLE IF EXISTS snapshots_fluxo_caixa',
            'DROP TABLE IF EXISTS indicadores_financeiros',
            'DROP TABLE IF EXISTS contas_financeiras_saldos_diarios',
            'DROP TABLE IF EXISTS dre_mapeamento_categorias',
            'DROP TABLE IF EXISTS dre_grupos',
            'DROP TABLE IF EXISTS lancamentos_contabeis_itens',
            'DROP TABLE IF EXISTS lancamentos_contabeis',
            'DROP TABLE IF EXISTS contas_contabeis',
            'DROP TABLE IF EXISTS notificacoes_sistema',
            'DROP TABLE IF EXISTS regras_automaticas_classificacao',
            'DROP TABLE IF EXISTS aprovacoes_titulos_itens',
            'DROP TABLE IF EXISTS aprovacoes_titulos',
            'DROP TABLE IF EXISTS titulos_comentarios',
            'DROP TABLE IF EXISTS tarefas_operacionais_bpo_historico',
            'DROP TABLE IF EXISTS tarefas_operacionais_bpo',
            'DROP TABLE IF EXISTS pix_eventos_webhook',
            'DROP TABLE IF EXISTS pix_recebimentos',
            'DROP TABLE IF EXISTS pix_cobrancas',
            'DROP TABLE IF EXISTS boletos_retorno_itens',
            'DROP TABLE IF EXISTS boletos_remessa_itens',
            'DROP TABLE IF EXISTS boletos_remessa',
            'DROP TABLE IF EXISTS borderos_recebimento_itens',
            'DROP TABLE IF EXISTS borderos_recebimento',
            'DROP TABLE IF EXISTS borderos_pagamento_itens',
            'DROP TABLE IF EXISTS borderos_pagamento',
            'DROP TABLE IF EXISTS integracoes_bancarias',
            'DROP TABLE IF EXISTS conciliacao_regras',
            'DROP TABLE IF EXISTS conciliacoes_bancarias',
            'DROP TABLE IF EXISTS extratos_bancarios',
            'DROP TABLE IF EXISTS anexos_financeiros',
            'DROP TABLE IF EXISTS movimentos_financeiros',
            'DROP TABLE IF EXISTS baixas',
            'DROP TABLE IF EXISTS titulos_parcelas',
            'DROP TABLE IF EXISTS titulos',
            'DROP TABLE IF EXISTS formas_pagamento',
            'DROP TABLE IF EXISTS contas_financeiras',
            'DROP TABLE IF EXISTS centros_custo',
            'DROP TABLE IF EXISTS categorias_financeiras',
            'DROP TABLE IF EXISTS bancos',
            'DROP TABLE IF EXISTS pessoa_contatos',
            'DROP TABLE IF EXISTS pessoa_enderecos',
            'DROP TABLE IF EXISTS pessoas',
            'DROP TABLE IF EXISTS unidades_negocio',
            'DROP TABLE IF EXISTS empresas',
            'DROP TABLE IF EXISTS perfil_acesso_permissoes',
            'DROP TABLE IF EXISTS perfis_acesso',
        ];
    }
}
