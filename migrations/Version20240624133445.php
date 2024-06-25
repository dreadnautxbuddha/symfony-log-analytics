<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Dreadnaut\LogAnalyticsBundle\Enum\Http\RequestMethod;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240624133445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $request_methods = implode(
            ', ',
            array_map(fn (string $name) => "'{$name}'", array_column(RequestMethod::cases(), 'name'))
        );

        $this->addSql("CREATE TYPE http_request_method AS ENUM ({$request_methods});");
        $this->addSql('
            ALTER TABLE log_entry
                ALTER COLUMN http_request_method TYPE http_request_method
            USING http_request_method::http_request_method
        ');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE log_entry
                ALTER COLUMN http_request_method SET NOT NULL,
                ALTER COLUMN http_request_method TYPE VARCHAR(255)
        ');
        $this->addSql('DROP TYPE http_request_method');
    }
}
