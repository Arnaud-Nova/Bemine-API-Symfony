<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322062628 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, wedding_id INT NOT NULL, name VARCHAR(100) DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_14B78418FCBBB0ED (wedding_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gift (id INT AUTO_INCREMENT NOT NULL, guest_group_id INT DEFAULT NULL, wedding_id INT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, url VARCHAR(255) NOT NULL, availability TINYINT(1) NOT NULL, INDEX IDX_A47C990D817E1138 (guest_group_id), INDEX IDX_A47C990DFCBBB0ED (wedding_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guest_group (id INT AUTO_INCREMENT NOT NULL, contact_person_id INT NOT NULL, wedding_id INT NOT NULL, email VARCHAR(100) DEFAULT NULL, slug_url VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_783ED3D14F8A983C (contact_person_id), INDEX IDX_783ED3D1FCBBB0ED (wedding_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_guest_group (id INT AUTO_INCREMENT NOT NULL, mail_id INT NOT NULL, guest_group_id INT NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_CCE1AE0CC8776F01 (mail_id), INDEX IDX_CCE1AE0C817E1138 (guest_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, guest_group_id INT DEFAULT NULL, reception_table_id INT DEFAULT NULL, wedding_id INT NOT NULL, lastname VARCHAR(100) DEFAULT NULL, firstname VARCHAR(50) NOT NULL, attendance TINYINT(1) DEFAULT NULL, newlyweds TINYINT(1) NOT NULL, menu VARCHAR(30) DEFAULT NULL, allergies TINYINT(1) DEFAULT \'0\' NOT NULL, halal TINYINT(1) DEFAULT \'0\' NOT NULL, no_alcohol TINYINT(1) DEFAULT \'0\' NOT NULL, vegetarian TINYINT(1) DEFAULT \'0\' NOT NULL, vegan TINYINT(1) DEFAULT \'0\' NOT NULL, casher TINYINT(1) DEFAULT \'0\' NOT NULL, comment_allergies VARCHAR(255) DEFAULT NULL, seat_number INT DEFAULT NULL, INDEX IDX_34DCD176817E1138 (guest_group_id), INDEX IDX_34DCD17640319578 (reception_table_id), INDEX IDX_34DCD176FCBBB0ED (wedding_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, wedding_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, url_avatar VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649FCBBB0ED (wedding_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, content LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, address VARCHAR(255) DEFAULT NULL, postcode INT DEFAULT NULL, city VARCHAR(80) DEFAULT NULL, schedule DATETIME DEFAULT NULL, informations LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_wedding (event_id INT NOT NULL, wedding_id INT NOT NULL, INDEX IDX_DE95F53971F7E88B (event_id), INDEX IDX_DE95F539FCBBB0ED (wedding_id), PRIMARY KEY(event_id, wedding_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reception_table (id INT AUTO_INCREMENT NOT NULL, wedding_id INT NOT NULL, name VARCHAR(50) NOT NULL, total_seats INT NOT NULL, INDEX IDX_9C6E232EFCBBB0ED (wedding_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wedding (id INT AUTO_INCREMENT NOT NULL, date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990D817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id)');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990DFCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('ALTER TABLE guest_group ADD CONSTRAINT FK_783ED3D14F8A983C FOREIGN KEY (contact_person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guest_group ADD CONSTRAINT FK_783ED3D1FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('ALTER TABLE mail_guest_group ADD CONSTRAINT FK_CCE1AE0CC8776F01 FOREIGN KEY (mail_id) REFERENCES mail (id)');
        $this->addSql('ALTER TABLE mail_guest_group ADD CONSTRAINT FK_CCE1AE0C817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD17640319578 FOREIGN KEY (reception_table_id) REFERENCES reception_table (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('ALTER TABLE event_wedding ADD CONSTRAINT FK_DE95F53971F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_wedding ADD CONSTRAINT FK_DE95F539FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reception_table ADD CONSTRAINT FK_9C6E232EFCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990D817E1138');
        $this->addSql('ALTER TABLE mail_guest_group DROP FOREIGN KEY FK_CCE1AE0C817E1138');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176817E1138');
        $this->addSql('ALTER TABLE guest_group DROP FOREIGN KEY FK_783ED3D14F8A983C');
        $this->addSql('ALTER TABLE mail_guest_group DROP FOREIGN KEY FK_CCE1AE0CC8776F01');
        $this->addSql('ALTER TABLE event_wedding DROP FOREIGN KEY FK_DE95F53971F7E88B');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD17640319578');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418FCBBB0ED');
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990DFCBBB0ED');
        $this->addSql('ALTER TABLE guest_group DROP FOREIGN KEY FK_783ED3D1FCBBB0ED');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176FCBBB0ED');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FCBBB0ED');
        $this->addSql('ALTER TABLE event_wedding DROP FOREIGN KEY FK_DE95F539FCBBB0ED');
        $this->addSql('ALTER TABLE reception_table DROP FOREIGN KEY FK_9C6E232EFCBBB0ED');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE gift');
        $this->addSql('DROP TABLE guest_group');
        $this->addSql('DROP TABLE mail_guest_group');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE mail');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_wedding');
        $this->addSql('DROP TABLE reception_table');
        $this->addSql('DROP TABLE wedding');
    }
}
