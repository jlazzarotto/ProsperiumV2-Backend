<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260315015200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Increase bancos.nome length to support enriched official catalog.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bancos MODIFY nome VARCHAR(180) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bancos MODIFY nome VARCHAR(120) NOT NULL');
    }
}
