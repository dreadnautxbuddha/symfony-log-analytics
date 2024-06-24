<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240624114313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE log_entry RENAME COLUMN http_protocol_version TO http_version;');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE log_entry RENAME COLUMN http_version TO http_protocol_version;');

    }
}
