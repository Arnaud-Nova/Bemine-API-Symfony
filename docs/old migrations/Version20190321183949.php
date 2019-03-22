<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190321183949 extends AbstractMigration
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
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE guest_group DROP FOREIGN KEY FK_783ED3D14F8A983C');
        $this->addSql('ALTER TABLE guest_group ADD CONSTRAINT FK_783ED3D14F8A983C FOREIGN KEY (contact_person_id) REFERENCES person (id)');
    }
}
