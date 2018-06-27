<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180626224950 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cleaning_item_options DROP FOREIGN KEY FK_E67F27C63E31DFF7');
        $this->addSql('ALTER TABLE cleaning_item_options ADD CONSTRAINT FK_E67F27C63E31DFF7 FOREIGN KEY (cleaning_item_id) REFERENCES cleaning_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_addresses DROP FOREIGN KEY FK_C4378D0C9395C3F3');
        $this->addSql('ALTER TABLE customer_addresses ADD CONSTRAINT FK_C4378D0C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE schedule ADD end_date_at DATETIME NOT NULL, ADD instructions LONGTEXT DEFAULT NULL, CHANGE date start_date_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE schedule_items DROP FOREIGN KEY FK_1D472911A40BC2D5');
        $this->addSql('ALTER TABLE schedule_items ADD CONSTRAINT FK_1D472911A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cleaning_item_options DROP FOREIGN KEY FK_E67F27C63E31DFF7');
        $this->addSql('ALTER TABLE cleaning_item_options ADD CONSTRAINT FK_E67F27C63E31DFF7 FOREIGN KEY (cleaning_item_id) REFERENCES cleaning_item (id)');
        $this->addSql('ALTER TABLE customer_addresses DROP FOREIGN KEY FK_C4378D0C9395C3F3');
        $this->addSql('ALTER TABLE customer_addresses ADD CONSTRAINT FK_C4378D0C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE schedule ADD date DATETIME NOT NULL, DROP start_date_at, DROP end_date_at, DROP instructions');
        $this->addSql('ALTER TABLE schedule_items DROP FOREIGN KEY FK_1D472911A40BC2D5');
        $this->addSql('ALTER TABLE schedule_items ADD CONSTRAINT FK_1D472911A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
    }
}
