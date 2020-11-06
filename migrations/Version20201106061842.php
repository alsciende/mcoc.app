<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201106061842 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE battlegroup (id INT AUTO_INCREMENT NOT NULL, alliance_id VARCHAR(255) NOT NULL, position INT NOT NULL, nickname VARCHAR(255) DEFAULT NULL, INDEX IDX_6782F74810A0EA3F (alliance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE battlegroup ADD CONSTRAINT FK_6782F74810A0EA3F FOREIGN KEY (alliance_id) REFERENCES alliance (id)');
        $this->addSql('ALTER TABLE roster ADD rating INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE battlegroup');
        $this->addSql('ALTER TABLE roster DROP rating');
    }
}
