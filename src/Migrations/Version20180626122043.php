<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180626122043 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, state VARCHAR(50) NOT NULL, items_total NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5A3811FB9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule_items (id INT AUTO_INCREMENT NOT NULL, cleaning_item_id INT NOT NULL, schedule_id INT NOT NULL, quantity SMALLINT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, total NUMERIC(10, 2) NOT NULL, INDEX IDX_1D4729113E31DFF7 (cleaning_item_id), INDEX IDX_1D472911A40BC2D5 (schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE schedule_items ADD CONSTRAINT FK_1D4729113E31DFF7 FOREIGN KEY (cleaning_item_id) REFERENCES cleaning_item (id)');
        $this->addSql('ALTER TABLE schedule_items ADD CONSTRAINT FK_1D472911A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
        $this->addSql('ALTER TABLE customer ADD email VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE schedule_items DROP FOREIGN KEY FK_1D472911A40BC2D5');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE schedule_items');
        $this->addSql('ALTER TABLE customer DROP email');
    }
}
