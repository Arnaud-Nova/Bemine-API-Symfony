<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190329092433 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mail_guest_group DROP FOREIGN KEY FK_CCE1AE0CC8776F01');
        $this->addSql('DROP TABLE mail');
        $this->addSql('DROP TABLE mail_guest_group');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mail (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci, content LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE mail_guest_group (id INT AUTO_INCREMENT NOT NULL, mail_id INT NOT NULL, guest_group_id INT NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_CCE1AE0C817E1138 (guest_group_id), INDEX IDX_CCE1AE0CC8776F01 (mail_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE mail_guest_group ADD CONSTRAINT FK_CCE1AE0C817E1138 FOREIGN KEY (guest_group_id) REFERENCES guest_group (id)');
        $this->addSql('ALTER TABLE mail_guest_group ADD CONSTRAINT FK_CCE1AE0CC8776F01 FOREIGN KEY (mail_id) REFERENCES mail (id)');
    }
}
