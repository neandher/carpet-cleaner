<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180629122148 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_A1ACE1586DE44026 ON zip_code (description)');
        $this->addSql('ALTER TABLE customer_addresses DROP FOREIGN KEY FK_C4378D0CF5B7AF75');
        $this->addSql('ALTER TABLE customer_addresses ADD CONSTRAINT FK_C4378D0CF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_addresses DROP FOREIGN KEY FK_C4378D0CF5B7AF75');
        $this->addSql('ALTER TABLE customer_addresses ADD CONSTRAINT FK_C4378D0CF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_A1ACE1586DE44026 ON zip_code');
    }
}
