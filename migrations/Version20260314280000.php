<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260314280000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add root/admin role model to users and simplify initial bootstrap flow.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE users ADD role VARCHAR(30) NOT NULL DEFAULT 'ROLE_ADMIN' AFTER password_hash");
        $this->addSql("UPDATE users SET role = 'ROLE_ADMIN' WHERE role IS NULL OR role = ''");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users DROP COLUMN role');
    }
}
