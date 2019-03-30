<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190328085640 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE guest_group_event (guest_group_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_ECB79A42817E1138 (guest_group_id), INDEX IDX_ECB79A4271F7E88B (event_id), PRIMARY KEY(guest_group_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guest_group_event ADD CONSTRAINT FK_ECB79A42817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guest_group_event ADD CONSTRAINT FK_ECB79A4271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE event_wedding');
        $this->addSql('ALTER TABLE person CHANGE attendance attendance INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD wedding_id INT DEFAULT NULL, ADD active TINYINT(1) DEFAULT NULL, ADD map LONGTEXT DEFAULT NULL, CHANGE name name VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7FCBBB0ED ON event (wedding_id)');
        $this->addSql('ALTER TABLE wedding CHANGE date date DATE DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE event_wedding (event_id INT NOT NULL, wedding_id INT NOT NULL, INDEX IDX_DE95F539FCBBB0ED (wedding_id), INDEX IDX_DE95F53971F7E88B (event_id), PRIMARY KEY(event_id, wedding_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE event_wedding ADD CONSTRAINT FK_DE95F53971F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_wedding ADD CONSTRAINT FK_DE95F539FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE guest_group_event');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7FCBBB0ED');
        $this->addSql('DROP INDEX IDX_3BAE0AA7FCBBB0ED ON event');
        $this->addSql('ALTER TABLE event DROP wedding_id, DROP active, DROP map, CHANGE name name VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE person CHANGE attendance attendance TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE wedding CHANGE date date DATETIME DEFAULT NULL');
    }
}
