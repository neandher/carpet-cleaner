<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180617142411 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cleaning_item_options (id INT AUTO_INCREMENT NOT NULL, cleaning_item_id INT NOT NULL, cleaning_item_option_id INT NOT NULL, amount NUMERIC(10, 0) NOT NULL, is_enabled TINYINT(1) NOT NULL, INDEX IDX_E67F27C63E31DFF7 (cleaning_item_id), INDEX IDX_E67F27C6B6C4FB08 (cleaning_item_option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cleaning_item_options ADD CONSTRAINT FK_E67F27C63E31DFF7 FOREIGN KEY (cleaning_item_id) REFERENCES cleaning_item (id)');
        $this->addSql('ALTER TABLE cleaning_item_options ADD CONSTRAINT FK_E67F27C6B6C4FB08 FOREIGN KEY (cleaning_item_option_id) REFERENCES cleaning_item_option (id)');
        $this->addSql('ALTER TABLE cleaning_item_option DROP FOREIGN KEY FK_1311B71A3E31DFF7');
        $this->addSql('DROP INDEX IDX_1311B71A3E31DFF7 ON cleaning_item_option');
        $this->addSql('ALTER TABLE cleaning_item_option DROP cleaning_item_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE cleaning_item_options');
        $this->addSql('ALTER TABLE cleaning_item_option ADD cleaning_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cleaning_item_option ADD CONSTRAINT FK_1311B71A3E31DFF7 FOREIGN KEY (cleaning_item_id) REFERENCES cleaning_item (id)');
        $this->addSql('CREATE INDEX IDX_1311B71A3E31DFF7 ON cleaning_item_option (cleaning_item_id)');
    }
}
