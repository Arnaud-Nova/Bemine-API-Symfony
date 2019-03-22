<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190321143349 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person ADD wedding_id INT NOT NULL, CHANGE newlyweds newlyweds TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176FCBBB0ED FOREIGN KEY (wedding_id) REFERENCES wedding (id)');
        $this->addSql('CREATE INDEX IDX_34DCD176FCBBB0ED ON person (wedding_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176FCBBB0ED');
        $this->addSql('DROP INDEX IDX_34DCD176FCBBB0ED ON person');
        $this->addSql('ALTER TABLE person DROP wedding_id, CHANGE newlyweds newlyweds TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
