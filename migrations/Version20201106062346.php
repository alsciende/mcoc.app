<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201106062346 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE defender ADD battlegroup_id VARCHAR(255) NOT NULL, DROP battlegroup');
        $this->addSql('ALTER TABLE defender ADD CONSTRAINT FK_55D46A2E40838C59 FOREIGN KEY (battlegroup_id) REFERENCES battlegroup (id)');
        $this->addSql('CREATE INDEX IDX_55D46A2E40838C59 ON defender (battlegroup_id)');
        $this->addSql('ALTER TABLE member ADD battlegroup_id VARCHAR(255) DEFAULT NULL, DROP battlegroup');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA7840838C59 FOREIGN KEY (battlegroup_id) REFERENCES battlegroup (id)');
        $this->addSql('CREATE INDEX IDX_70E4FA7840838C59 ON member (battlegroup_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE defender DROP FOREIGN KEY FK_55D46A2E40838C59');
        $this->addSql('DROP INDEX IDX_55D46A2E40838C59 ON defender');
        $this->addSql('ALTER TABLE defender ADD battlegroup INT NOT NULL, DROP battlegroup_id');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA7840838C59');
        $this->addSql('DROP INDEX IDX_70E4FA7840838C59 ON member');
        $this->addSql('ALTER TABLE member ADD battlegroup INT DEFAULT NULL, DROP battlegroup_id');
    }
}
