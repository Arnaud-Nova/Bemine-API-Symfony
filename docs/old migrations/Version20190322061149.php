<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322061149 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE guest_group DROP FOREIGN KEY FK_783ED3D14F8A983C');
        $this->addSql('ALTER TABLE guest_group ADD CONSTRAINT FK_783ED3D14F8A983C FOREIGN KEY (contact_person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176817E1138');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_34DCD176FCBBB0ED ON person (wedding_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE guest_group DROP FOREIGN KEY FK_783ED3D14F8A983C');
        $this->addSql('ALTER TABLE guest_group ADD CONSTRAINT FK_783ED3D14F8A983C FOREIGN KEY (contact_person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176FCBBB0ED');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176817E1138');
        $this->addSql('DROP INDEX IDX_34DCD176FCBBB0ED ON person');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id)');
    }
}
