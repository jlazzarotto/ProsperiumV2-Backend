<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260314241000 extends AbstractMigration
{
    public function getDescription(): string { return 'Normalize cobranca foreign key index names to Doctrine naming conventions.'; }
    public function up(Schema $schema): void
    {
        $this->renameIndexIfExists('borderos_pagamento_itens', 'fk_bpgi_bordero', 'IDX_445F11BB8C963B4B');
        $this->renameIndexIfExists('borderos_pagamento_itens', 'fk_bpgi_parcela', 'IDX_445F11BBA57188A');
        $this->renameIndexIfExists('pix_recebimentos', 'fk_pixr_company', 'IDX_FFBBE665979B1AD6');
        $this->renameIndexIfExists('pix_recebimentos', 'fk_pixr_empresa', 'IDX_FFBBE665521E1991');
        $this->renameIndexIfExists('pix_recebimentos', 'fk_pixr_unidade', 'IDX_FFBBE665EDF4B99B');
        $this->renameIndexIfExists('pix_recebimentos', 'fk_pixr_cobranca', 'IDX_FFBBE665F311FB1F');
        $this->renameIndexIfExists('borderos_pagamento', 'fk_bpg_company', 'IDX_DF3ED3C2979B1AD6');
        $this->renameIndexIfExists('borderos_pagamento', 'fk_bpg_empresa', 'IDX_DF3ED3C2521E1991');
        $this->renameIndexIfExists('borderos_pagamento', 'fk_bpg_unidade', 'IDX_DF3ED3C2EDF4B99B');
        $this->renameIndexIfExists('borderos_pagamento', 'fk_bpg_conta', 'IDX_DF3ED3C232F8769B');
        $this->renameIndexIfExists('pix_eventos_webhook', 'fk_pixw_empresa', 'IDX_F495EB49521E1991');
        $this->renameIndexIfExists('pix_eventos_webhook', 'fk_pixw_unidade', 'IDX_F495EB49EDF4B99B');
        $this->renameIndexIfExists('boletos_remessa', 'fk_brem_empresa', 'IDX_CE2192D5521E1991');
        $this->renameIndexIfExists('boletos_remessa', 'fk_brem_unidade', 'IDX_CE2192D5EDF4B99B');
        $this->renameIndexIfExists('boletos_remessa', 'fk_brem_conta', 'IDX_CE2192D532F8769B');
        $this->renameIndexIfExists('pix_cobrancas', 'fk_pixc_empresa', 'IDX_BAB32D48521E1991');
        $this->renameIndexIfExists('pix_cobrancas', 'fk_pixc_unidade', 'IDX_BAB32D48EDF4B99B');
        $this->renameIndexIfExists('pix_cobrancas', 'fk_pixc_parcela', 'IDX_BAB32D48A57188A');
        $this->renameIndexIfExists('pix_cobrancas', 'fk_pixc_conta', 'IDX_BAB32D4832F8769B');
        $this->renameIndexIfExists('borderos_recebimento_itens', 'fk_brci_bordero', 'IDX_A1206A61A7B6D2F7');
        $this->renameIndexIfExists('borderos_recebimento_itens', 'fk_brci_parcela', 'IDX_A1206A61A57188A');
        $this->renameIndexIfExists('boletos_retorno_itens', 'fk_bret_empresa', 'IDX_48E2FD8C521E1991');
        $this->renameIndexIfExists('boletos_retorno_itens', 'fk_bret_unidade', 'IDX_48E2FD8CEDF4B99B');
        $this->renameIndexIfExists('boletos_retorno_itens', 'fk_bret_item', 'IDX_48E2FD8C1E9DA45D');
        $this->renameIndexIfExists('borderos_recebimento', 'fk_brc_company', 'IDX_96FDED9A979B1AD6');
        $this->renameIndexIfExists('borderos_recebimento', 'fk_brc_empresa', 'IDX_96FDED9A521E1991');
        $this->renameIndexIfExists('borderos_recebimento', 'fk_brc_unidade', 'IDX_96FDED9AEDF4B99B');
        $this->renameIndexIfExists('borderos_recebimento', 'fk_brc_conta', 'IDX_96FDED9A32F8769B');
        $this->renameIndexIfExists('boletos_remessa_itens', 'fk_bri_remessa', 'IDX_2A81DCEF42933351');
        $this->renameIndexIfExists('boletos_remessa_itens', 'fk_bri_parcela', 'IDX_2A81DCEFA57188A');
    }
    public function down(Schema $schema): void
    {
        $this->renameIndexIfExists('borderos_pagamento_itens', 'IDX_445F11BB8C963B4B', 'fk_bpgi_bordero');
        $this->renameIndexIfExists('borderos_pagamento_itens', 'IDX_445F11BBA57188A', 'fk_bpgi_parcela');
        $this->renameIndexIfExists('pix_recebimentos', 'IDX_FFBBE665979B1AD6', 'fk_pixr_company');
        $this->renameIndexIfExists('pix_recebimentos', 'IDX_FFBBE665521E1991', 'fk_pixr_empresa');
        $this->renameIndexIfExists('pix_recebimentos', 'IDX_FFBBE665EDF4B99B', 'fk_pixr_unidade');
        $this->renameIndexIfExists('pix_recebimentos', 'IDX_FFBBE665F311FB1F', 'fk_pixr_cobranca');
        $this->renameIndexIfExists('borderos_pagamento', 'IDX_DF3ED3C2979B1AD6', 'fk_bpg_company');
        $this->renameIndexIfExists('borderos_pagamento', 'IDX_DF3ED3C2521E1991', 'fk_bpg_empresa');
        $this->renameIndexIfExists('borderos_pagamento', 'IDX_DF3ED3C2EDF4B99B', 'fk_bpg_unidade');
        $this->renameIndexIfExists('borderos_pagamento', 'IDX_DF3ED3C232F8769B', 'fk_bpg_conta');
        $this->renameIndexIfExists('pix_eventos_webhook', 'IDX_F495EB49521E1991', 'fk_pixw_empresa');
        $this->renameIndexIfExists('pix_eventos_webhook', 'IDX_F495EB49EDF4B99B', 'fk_pixw_unidade');
        $this->renameIndexIfExists('boletos_remessa', 'IDX_CE2192D5521E1991', 'fk_brem_empresa');
        $this->renameIndexIfExists('boletos_remessa', 'IDX_CE2192D5EDF4B99B', 'fk_brem_unidade');
        $this->renameIndexIfExists('boletos_remessa', 'IDX_CE2192D532F8769B', 'fk_brem_conta');
        $this->renameIndexIfExists('pix_cobrancas', 'IDX_BAB32D48521E1991', 'fk_pixc_empresa');
        $this->renameIndexIfExists('pix_cobrancas', 'IDX_BAB32D48EDF4B99B', 'fk_pixc_unidade');
        $this->renameIndexIfExists('pix_cobrancas', 'IDX_BAB32D48A57188A', 'fk_pixc_parcela');
        $this->renameIndexIfExists('pix_cobrancas', 'IDX_BAB32D4832F8769B', 'fk_pixc_conta');
        $this->renameIndexIfExists('borderos_recebimento_itens', 'IDX_A1206A61A7B6D2F7', 'fk_brci_bordero');
        $this->renameIndexIfExists('borderos_recebimento_itens', 'IDX_A1206A61A57188A', 'fk_brci_parcela');
        $this->renameIndexIfExists('boletos_retorno_itens', 'IDX_48E2FD8C521E1991', 'fk_bret_empresa');
        $this->renameIndexIfExists('boletos_retorno_itens', 'IDX_48E2FD8CEDF4B99B', 'fk_bret_unidade');
        $this->renameIndexIfExists('boletos_retorno_itens', 'IDX_48E2FD8C1E9DA45D', 'fk_bret_item');
        $this->renameIndexIfExists('borderos_recebimento', 'IDX_96FDED9A979B1AD6', 'fk_brc_company');
        $this->renameIndexIfExists('borderos_recebimento', 'IDX_96FDED9A521E1991', 'fk_brc_empresa');
        $this->renameIndexIfExists('borderos_recebimento', 'IDX_96FDED9AEDF4B99B', 'fk_brc_unidade');
        $this->renameIndexIfExists('borderos_recebimento', 'IDX_96FDED9A32F8769B', 'fk_brc_conta');
        $this->renameIndexIfExists('boletos_remessa_itens', 'IDX_2A81DCEF42933351', 'fk_bri_remessa');
        $this->renameIndexIfExists('boletos_remessa_itens', 'IDX_2A81DCEFA57188A', 'fk_bri_parcela');
    }
    private function renameIndexIfExists(string $table, string $from, string $to): void
    {
        $existsFrom = (int) $this->connection->fetchOne('SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?', [$table, $from]) > 0;
        $existsTo = (int) $this->connection->fetchOne('SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?', [$table, $to]) > 0;
        if ($existsFrom && !$existsTo) { $this->connection->executeStatement(sprintf('ALTER TABLE %s RENAME INDEX %s TO %s', $table, $from, $to)); }
    }
}
