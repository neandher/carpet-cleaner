<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180630224724 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, street VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, district VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(50) NOT NULL, state VARCHAR(255) NOT NULL, complement VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cleaning_item (id INT AUTO_INCREMENT NOT NULL, cleaning_item_category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, display_order SMALLINT NOT NULL, max_quantity SMALLINT NOT NULL, amount NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, INDEX IDX_9FD2CF4B719336CF (cleaning_item_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cleaning_item_category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, display_order SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cleaning_item_option (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cleaning_item_options (id INT AUTO_INCREMENT NOT NULL, cleaning_item_id INT NOT NULL, cleaning_item_option_id INT NOT NULL, percentage NUMERIC(10, 2) NOT NULL, is_enabled TINYINT(1) NOT NULL, INDEX IDX_E67F27C63E31DFF7 (cleaning_item_id), INDEX IDX_E67F27C6B6C4FB08 (cleaning_item_option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(60) NOT NULL, is_enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_81398E096B01BC5B (phone_number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_addresses (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, address_id INT NOT NULL, INDEX IDX_C4378D0C9395C3F3 (customer_id), INDEX IDX_C4378D0CF5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotion_coupon (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, code VARCHAR(20) NOT NULL, percentage NUMERIC(10, 2) NOT NULL, expires_at DATETIME DEFAULT NULL, usage_limit SMALLINT DEFAULT NULL, used SMALLINT DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7105143F77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, promotion_coupon_id INT DEFAULT NULL, state VARCHAR(50) NOT NULL, items_discount NUMERIC(10, 2) DEFAULT NULL, items_sub_total NUMERIC(10, 2) NOT NULL, items_total NUMERIC(10, 2) NOT NULL, start_date_at DATETIME NOT NULL, end_date_at DATETIME NOT NULL, instructions LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5A3811FB9395C3F3 (customer_id), INDEX IDX_5A3811FB17B24436 (promotion_coupon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule_items (id INT AUTO_INCREMENT NOT NULL, cleaning_item_id INT NOT NULL, cleaning_item_option_id INT NOT NULL, schedule_id INT NOT NULL, quantity SMALLINT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, total NUMERIC(10, 2) NOT NULL, INDEX IDX_1D4729113E31DFF7 (cleaning_item_id), INDEX IDX_1D472911B6C4FB08 (cleaning_item_option_id), INDEX IDX_1D472911A40BC2D5 (schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, receive_emails TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zip_code (id INT AUTO_INCREMENT NOT NULL, description SMALLINT NOT NULL, percentage NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_A1ACE1586DE44026 (description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cleaning_item ADD CONSTRAINT FK_9FD2CF4B719336CF FOREIGN KEY (cleaning_item_category_id) REFERENCES cleaning_item_category (id)');
        $this->addSql('ALTER TABLE cleaning_item_options ADD CONSTRAINT FK_E67F27C63E31DFF7 FOREIGN KEY (cleaning_item_id) REFERENCES cleaning_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cleaning_item_options ADD CONSTRAINT FK_E67F27C6B6C4FB08 FOREIGN KEY (cleaning_item_option_id) REFERENCES cleaning_item_option (id)');
        $this->addSql('ALTER TABLE customer_addresses ADD CONSTRAINT FK_C4378D0C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_addresses ADD CONSTRAINT FK_C4378D0CF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB17B24436 FOREIGN KEY (promotion_coupon_id) REFERENCES promotion_coupon (id)');
        $this->addSql('ALTER TABLE schedule_items ADD CONSTRAINT FK_1D4729113E31DFF7 FOREIGN KEY (cleaning_item_id) REFERENCES cleaning_item (id)');
        $this->addSql('ALTER TABLE schedule_items ADD CONSTRAINT FK_1D472911B6C4FB08 FOREIGN KEY (cleaning_item_option_id) REFERENCES cleaning_item_option (id)');
        $this->addSql('ALTER TABLE schedule_items ADD CONSTRAINT FK_1D472911A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_addresses DROP FOREIGN KEY FK_C4378D0CF5B7AF75');
        $this->addSql('ALTER TABLE cleaning_item_options DROP FOREIGN KEY FK_E67F27C63E31DFF7');
        $this->addSql('ALTER TABLE schedule_items DROP FOREIGN KEY FK_1D4729113E31DFF7');
        $this->addSql('ALTER TABLE cleaning_item DROP FOREIGN KEY FK_9FD2CF4B719336CF');
        $this->addSql('ALTER TABLE cleaning_item_options DROP FOREIGN KEY FK_E67F27C6B6C4FB08');
        $this->addSql('ALTER TABLE schedule_items DROP FOREIGN KEY FK_1D472911B6C4FB08');
        $this->addSql('ALTER TABLE customer_addresses DROP FOREIGN KEY FK_C4378D0C9395C3F3');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB9395C3F3');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB17B24436');
        $this->addSql('ALTER TABLE schedule_items DROP FOREIGN KEY FK_1D472911A40BC2D5');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE cleaning_item');
        $this->addSql('DROP TABLE cleaning_item_category');
        $this->addSql('DROP TABLE cleaning_item_option');
        $this->addSql('DROP TABLE cleaning_item_options');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE customer_addresses');
        $this->addSql('DROP TABLE promotion_coupon');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE schedule_items');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE zip_code');
    }
}
