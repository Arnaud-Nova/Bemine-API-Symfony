<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190320203322 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990D2F68B530');
        $this->addSql('ALTER TABLE mail_group DROP FOREIGN KEY FK_5903E6AA2F68B530');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1762F68B530');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD17673B8532F');
        $this->addSql('CREATE TABLE guest_group (id INT AUTO_INCREMENT NOT NULL, contact_person_id INT NOT NULL, wedding_id INT NOT NULL, email VARCHAR(100) DEFAULT NULL, slug_url VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_783ED3D14F8A983C (contact_person_id), INDEX IDX_783ED3D1FCBBB0ED (wedding_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_guest_group (id INT AUTO_INCREMENT NOT NULL, mail_id INT NOT NULL, guest_group_id INT NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_CCE1AE0CC8776F01 (mail_id), INDEX IDX_CCE1AE0C817E1138 (guest_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_wedding (event_id INT NOT NULL, wedding_id INT NOT NULL, INDEX IDX_DE95F53971F7E88B (event_id), INDEX IDX_DE95F539FCBBB0ED (wedding_id), PRIMARY KEY(event_id, wedding_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reception_table (id INT AUTO_INCREMENT NOT NULL, wedding_id INT NOT NULL, name VARCHAR(50) NOT NULL, total_seats INT NOT NULL, INDEX IDX_9C6E232EFCBBB0ED (wedding_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guest_group ADD CONSTRAINT FK_783ED3D14F8A983C FOREIGN KEY (contact_person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE guest_group ADD CONSTRAINT FK_783ED3D1FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('ALTER TABLE mail_guest_group ADD CONSTRAINT FK_CCE1AE0CC8776F01 FOREIGN KEY (mail_id) REFERENCES mail (id)');
        $this->addSql('ALTER TABLE mail_guest_group ADD CONSTRAINT FK_CCE1AE0C817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id)');
        $this->addSql('ALTER TABLE event_wedding ADD CONSTRAINT FK_DE95F53971F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_wedding ADD CONSTRAINT FK_DE95F539FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reception_table ADD CONSTRAINT FK_9C6E232EFCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE mail_group');
        $this->addSql('DROP TABLE `table`');
        $this->addSql('DROP TABLE wedding_event');
        $this->addSql('DROP INDEX IDX_A47C990D2F68B530 ON gift');
        $this->addSql('ALTER TABLE gift CHANGE group_id_id guest_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990D817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id)');
        $this->addSql('CREATE INDEX IDX_A47C990D817E1138 ON gift (guest_group_id)');
        $this->addSql('DROP INDEX IDX_34DCD17673B8532F ON person');
        $this->addSql('DROP INDEX IDX_34DCD1762F68B530 ON person');
        $this->addSql('ALTER TABLE person ADD guest_group_id INT DEFAULT NULL, ADD reception_table_id INT DEFAULT NULL, DROP group_id_id, DROP table_id_id, CHANGE allergies allergies TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD17640319578 FOREIGN KEY (reception_table_id) REFERENCES reception_table (id)');
        $this->addSql('CREATE INDEX IDX_34DCD176817E1138 ON person (guest_group_id)');
        $this->addSql('CREATE INDEX IDX_34DCD17640319578 ON person (reception_table_id)');
        $this->addSql('ALTER TABLE user ADD url_avatar VARCHAR(255) DEFAULT NULL, CHANGE wedding_id wedding_id INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990D817E1138');
        $this->addSql('ALTER TABLE mail_guest_group DROP FOREIGN KEY FK_CCE1AE0C817E1138');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176817E1138');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD17640319578');
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, contact_person_id INT NOT NULL, wedding_id INT NOT NULL, email VARCHAR(100) DEFAULT NULL COLLATE utf8mb4_unicode_ci, slug_url VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_6DC044C5FCBBB0ED (wedding_id), UNIQUE INDEX UNIQ_6DC044C54F8A983C (contact_person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE mail_group (id INT AUTO_INCREMENT NOT NULL, mail_id INT NOT NULL, group_id_id INT NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_5903E6AAC8776F01 (mail_id), INDEX IDX_5903E6AA2F68B530 (group_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE `table` (id INT AUTO_INCREMENT NOT NULL, wedding_id INT NOT NULL, name VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, total_seats INT NOT NULL, INDEX IDX_F6298F46FCBBB0ED (wedding_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE wedding_event (wedding_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_3BE7D673FCBBB0ED (wedding_id), INDEX IDX_3BE7D67371F7E88B (event_id), PRIMARY KEY(wedding_id, event_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C54F8A983C FOREIGN KEY (contact_person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('ALTER TABLE mail_group ADD CONSTRAINT FK_5903E6AA2F68B530 FOREIGN KEY (group_id_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE mail_group ADD CONSTRAINT FK_5903E6AAC8776F01 FOREIGN KEY (mail_id) REFERENCES mail (id)');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F46FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('ALTER TABLE wedding_event ADD CONSTRAINT FK_3BE7D67371F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wedding_event ADD CONSTRAINT FK_3BE7D673FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE guest_group');
        $this->addSql('DROP TABLE mail_guest_group');
        $this->addSql('DROP TABLE event_wedding');
        $this->addSql('DROP TABLE reception_table');
        $this->addSql('DROP INDEX IDX_A47C990D817E1138 ON gift');
        $this->addSql('ALTER TABLE gift CHANGE guest_group_id group_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990D2F68B530 FOREIGN KEY (group_id_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_A47C990D2F68B530 ON gift (group_id_id)');
        $this->addSql('DROP INDEX IDX_34DCD176817E1138 ON person');
        $this->addSql('DROP INDEX IDX_34DCD17640319578 ON person');
        $this->addSql('ALTER TABLE person ADD group_id_id INT DEFAULT NULL, ADD table_id_id INT DEFAULT NULL, DROP guest_group_id, DROP reception_table_id, CHANGE allergies allergies TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1762F68B530 FOREIGN KEY (group_id_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD17673B8532F FOREIGN KEY (table_id_id) REFERENCES `table` (id)');
        $this->addSql('CREATE INDEX IDX_34DCD17673B8532F ON person (table_id_id)');
        $this->addSql('CREATE INDEX IDX_34DCD1762F68B530 ON person (group_id_id)');
        $this->addSql('ALTER TABLE user DROP url_avatar, CHANGE wedding_id wedding_id INT NOT NULL, CHANGE roles roles VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
