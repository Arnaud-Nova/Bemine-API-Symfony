<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190327114042 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wedding_event ADD wedding_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wedding_event ADD CONSTRAINT FK_3BE7D673FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('CREATE INDEX IDX_3BE7D673FCBBB0ED ON wedding_event (wedding_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wedding_event DROP FOREIGN KEY FK_3BE7D673FCBBB0ED');
        $this->addSql('DROP INDEX IDX_3BE7D673FCBBB0ED ON wedding_event');
        $this->addSql('ALTER TABLE wedding_event DROP wedding_id');
    }
}
