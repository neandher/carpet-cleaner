<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180629213856 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE schedule_items ADD cleaning_item_option_id INT NOT NULL');
        $this->addSql('ALTER TABLE schedule_items ADD CONSTRAINT FK_1D472911B6C4FB08 FOREIGN KEY (cleaning_item_option_id) REFERENCES cleaning_item_option (id)');
        $this->addSql('CREATE INDEX IDX_1D472911B6C4FB08 ON schedule_items (cleaning_item_option_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE schedule_items DROP FOREIGN KEY FK_1D472911B6C4FB08');
        $this->addSql('DROP INDEX IDX_1D472911B6C4FB08 ON schedule_items');
        $this->addSql('ALTER TABLE schedule_items DROP cleaning_item_option_id');
    }
}
