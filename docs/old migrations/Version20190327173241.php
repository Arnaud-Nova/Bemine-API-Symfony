<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190327173241 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE guest_group_wedding_event DROP FOREIGN KEY FK_137B50D9FA0640');
        $this->addSql('CREATE TABLE guest_group_event (guest_group_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_ECB79A42817E1138 (guest_group_id), INDEX IDX_ECB79A4271F7E88B (event_id), PRIMARY KEY(guest_group_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guest_group_event ADD CONSTRAINT FK_ECB79A42817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guest_group_event ADD CONSTRAINT FK_ECB79A4271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE guest_group_wedding_event');
        $this->addSql('DROP TABLE wedding_event');
        $this->addSql('ALTER TABLE event ADD wedding_id INT DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD postcode INT DEFAULT NULL, ADD city VARCHAR(80) DEFAULT NULL, ADD schedule DATETIME DEFAULT NULL, ADD informations LONGTEXT DEFAULT NULL, ADD active TINYINT(1) DEFAULT NULL, ADD map LONGTEXT DEFAULT NULL, DROP name');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7FCBBB0ED ON event (wedding_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE guest_group_wedding_event (guest_group_id INT NOT NULL, wedding_event_id INT NOT NULL, INDEX IDX_137B50817E1138 (guest_group_id), INDEX IDX_137B50D9FA0640 (wedding_event_id), PRIMARY KEY(guest_group_id, wedding_event_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE wedding_event (id INT AUTO_INCREMENT NOT NULL, wedding_id INT DEFAULT NULL, event_id INT DEFAULT NULL, address VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, postcode INT DEFAULT NULL, city VARCHAR(80) DEFAULT NULL COLLATE utf8mb4_unicode_ci, schedule DATETIME DEFAULT NULL, informations LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, active TINYINT(1) DEFAULT NULL, map LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_3BE7D673FCBBB0ED (wedding_id), INDEX IDX_3BE7D67371F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE guest_group_wedding_event ADD CONSTRAINT FK_137B50817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guest_group_wedding_event ADD CONSTRAINT FK_137B50D9FA0640 FOREIGN KEY (wedding_event_id) REFERENCES wedding_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wedding_event ADD CONSTRAINT FK_3BE7D67371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE wedding_event ADD CONSTRAINT FK_3BE7D673FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('DROP TABLE guest_group_event');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7FCBBB0ED');
        $this->addSql('DROP INDEX IDX_3BAE0AA7FCBBB0ED ON event');
        $this->addSql('ALTER TABLE event ADD name VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, DROP wedding_id, DROP address, DROP postcode, DROP city, DROP schedule, DROP informations, DROP active, DROP map');
    }
}
