<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190403082138 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE roles roles VARCHAR(191) NOT NULL, CHANGE url_avatar url_avatar VARCHAR(191) DEFAULT NULL, CHANGE api_token api_token VARCHAR(191) DEFAULT NULL');
        $this->addSql('ALTER TABLE reception_table CHANGE total_seats total_seats INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reception_table CHANGE total_seats total_seats INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE url_avatar url_avatar VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE api_token api_token VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
