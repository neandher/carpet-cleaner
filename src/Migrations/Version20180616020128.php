<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180616020128 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cleaning_item_category ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD is_enabled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE cleaning_item ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD is_enabled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE cleaning_item_option ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD is_enabled TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cleaning_item DROP created_at, DROP updated_at, DROP is_enabled');
        $this->addSql('ALTER TABLE cleaning_item_category DROP created_at, DROP updated_at, DROP is_enabled');
        $this->addSql('ALTER TABLE cleaning_item_option DROP created_at, DROP updated_at, DROP is_enabled');
    }
}
