<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314310000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add bancos catalog and banking fields to contas_financeiras.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE bancos (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, codigo_compe VARCHAR(10) NOT NULL, nome VARCHAR(120) NOT NULL, ispb VARCHAR(10) DEFAULT NULL, status VARCHAR(20) NOT NULL DEFAULT 'active', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE KEY uk_bancos_codigo_compe (codigo_compe)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("ALTER TABLE contas_financeiras ADD banco_id BIGINT UNSIGNED DEFAULT NULL, ADD agencia VARCHAR(20) DEFAULT NULL, ADD conta_numero VARCHAR(30) DEFAULT NULL, ADD conta_digito VARCHAR(5) DEFAULT NULL, ADD titular_pessoa_id BIGINT UNSIGNED DEFAULT NULL, ADD saldo_inicial DECIMAL(18,2) NOT NULL DEFAULT 0.00, ADD data_saldo_inicial DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)', ADD permite_movimento_negativo TINYINT(1) NOT NULL DEFAULT 0");
        $this->addSql("ALTER TABLE contas_financeiras ADD CONSTRAINT FK_CONTA_FIN_BANCO FOREIGN KEY (banco_id) REFERENCES bancos (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE contas_financeiras ADD CONSTRAINT FK_CONTA_FIN_TITULAR FOREIGN KEY (titular_pessoa_id) REFERENCES pessoas (id) ON DELETE SET NULL");
        $this->addSql("CREATE INDEX idx_contas_financeiras_banco ON contas_financeiras (banco_id)");
        $this->addSql("CREATE INDEX idx_contas_financeiras_titular ON contas_financeiras (titular_pessoa_id)");

        foreach ([
            ['001', 'Banco do Brasil S.A.', '00000000'],
            ['033', 'Banco Santander (Brasil) S.A.', '90400888'],
            ['104', 'Caixa Econômica Federal', '00360305'],
            ['237', 'Banco Bradesco S.A.', '60746948'],
            ['341', 'Itaú Unibanco S.A.', '60701190'],
            ['748', 'Banco Cooperativo Sicredi S.A.', '01181521'],
            ['756', 'Banco Cooperativo do Brasil S.A. - Sicoob', '02038232'],
            ['077', 'Banco Inter S.A.', '00416968'],
            ['260', 'Nu Pagamentos S.A. - Nubank', '18236120'],
        ] as [$codigoCompe, $nome, $ispb]) {
            $this->addSql("INSERT INTO bancos (codigo_compe, nome, ispb, status, created_at) SELECT '$codigoCompe', '$nome', '$ispb', 'active', NOW() WHERE NOT EXISTS (SELECT 1 FROM bancos WHERE codigo_compe = '$codigoCompe')");
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contas_financeiras DROP FOREIGN KEY FK_CONTA_FIN_BANCO');
        $this->addSql('ALTER TABLE contas_financeiras DROP FOREIGN KEY FK_CONTA_FIN_TITULAR');
        $this->addSql('DROP INDEX idx_contas_financeiras_banco ON contas_financeiras');
        $this->addSql('DROP INDEX idx_contas_financeiras_titular ON contas_financeiras');
        $this->addSql('ALTER TABLE contas_financeiras DROP banco_id, DROP agencia, DROP conta_numero, DROP conta_digito, DROP titular_pessoa_id, DROP saldo_inicial, DROP data_saldo_inicial, DROP permite_movimento_negativo');
        $this->addSql('DROP TABLE IF EXISTS bancos');
    }
}
