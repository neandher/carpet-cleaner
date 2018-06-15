<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180615213053 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cleaning_item (id INT AUTO_INCREMENT NOT NULL, cleaning_item_category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, max_quantity SMALLINT NOT NULL, INDEX IDX_9FD2CF4B719336CF (cleaning_item_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cleaning_item_category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cleaning_item_option (id INT AUTO_INCREMENT NOT NULL, cleaning_item_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_1311B71A3E31DFF7 (cleaning_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cleaning_item ADD CONSTRAINT FK_9FD2CF4B719336CF FOREIGN KEY (cleaning_item_category_id) REFERENCES cleaning_item_category (id)');
        $this->addSql('ALTER TABLE cleaning_item_option ADD CONSTRAINT FK_1311B71A3E31DFF7 FOREIGN KEY (cleaning_item_id) REFERENCES cleaning_item (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cleaning_item_option DROP FOREIGN KEY FK_1311B71A3E31DFF7');
        $this->addSql('ALTER TABLE cleaning_item DROP FOREIGN KEY FK_9FD2CF4B719336CF');
        $this->addSql('DROP TABLE cleaning_item');
        $this->addSql('DROP TABLE cleaning_item_category');
        $this->addSql('DROP TABLE cleaning_item_option');
    }
}
