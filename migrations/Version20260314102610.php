<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314102610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Legacy placeholder migration kept for migration history compatibility.';
    }

    public function up(Schema $schema): void
    {
    }

    public function down(Schema $schema): void
    {
    }
}
