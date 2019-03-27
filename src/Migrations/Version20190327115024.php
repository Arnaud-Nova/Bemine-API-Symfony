<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190327115024 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE guest_group_wedding_event (guest_group_id INT NOT NULL, wedding_event_id INT NOT NULL, INDEX IDX_137B50817E1138 (guest_group_id), INDEX IDX_137B50D9FA0640 (wedding_event_id), PRIMARY KEY(guest_group_id, wedding_event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guest_group_wedding_event ADD CONSTRAINT FK_137B50817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guest_group_wedding_event ADD CONSTRAINT FK_137B50D9FA0640 FOREIGN KEY (wedding_event_id) REFERENCES wedding_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wedding_event ADD event_id INT DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD postcode INT DEFAULT NULL, ADD city VARCHAR(80) DEFAULT NULL, ADD schedule DATETIME DEFAULT NULL, ADD informations LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE wedding_event ADD CONSTRAINT FK_3BE7D67371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_3BE7D67371F7E88B ON wedding_event (event_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE guest_group_wedding_event');
        $this->addSql('ALTER TABLE wedding_event DROP FOREIGN KEY FK_3BE7D67371F7E88B');
        $this->addSql('DROP INDEX IDX_3BE7D67371F7E88B ON wedding_event');
        $this->addSql('ALTER TABLE wedding_event DROP event_id, DROP address, DROP postcode, DROP city, DROP schedule, DROP informations');
    }
}
