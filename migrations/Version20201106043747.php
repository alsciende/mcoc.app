<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201106043747 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alliance (id VARCHAR(255) NOT NULL, tag VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6CBA583F389B783 (tag), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidate (id VARCHAR(255) NOT NULL, alliance_id VARCHAR(255) NOT NULL, player_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, is_rejected TINYINT(1) NOT NULL, INDEX IDX_C8B28E4410A0EA3F (alliance_id), INDEX IDX_C8B28E4499E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE champion (id VARCHAR(255) NOT NULL, character_id VARCHAR(255) DEFAULT NULL, tier INT NOT NULL, INDEX IDX_45437EB41136BE75 (character_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `character` (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX name_idx (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE external_character (id VARCHAR(255) NOT NULL, character_id VARCHAR(255) DEFAULT NULL, external_id VARCHAR(255) NOT NULL, source VARCHAR(255) NOT NULL, INDEX IDX_B7211F201136BE75 (character_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE external_user (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL, source VARCHAR(255) NOT NULL, INDEX IDX_188CB665A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member (id VARCHAR(255) NOT NULL, alliance_id VARCHAR(255) NOT NULL, player_id VARCHAR(255) NOT NULL, role INT NOT NULL, battlegroup INT DEFAULT NULL, INDEX IDX_70E4FA7810A0EA3F (alliance_id), UNIQUE INDEX UNIQ_70E4FA7899E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, member_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_98197A65A76ED395 (user_id), UNIQUE INDEX UNIQ_98197A657597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roster (id VARCHAR(255) NOT NULL, player_id VARCHAR(255) NOT NULL, champion_id VARCHAR(255) NOT NULL, rank INT NOT NULL, signature INT NOT NULL, INDEX IDX_60B9ADF999E6F5DF (player_id), INDEX IDX_60B9ADF9FA7FD7EB (champion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id VARCHAR(255) NOT NULL, active_player_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D6498F70B6EB (active_player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E4410A0EA3F FOREIGN KEY (alliance_id) REFERENCES alliance (id)');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E4499E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE champion ADD CONSTRAINT FK_45437EB41136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id)');
        $this->addSql('ALTER TABLE external_character ADD CONSTRAINT FK_B7211F201136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id)');
        $this->addSql('ALTER TABLE external_user ADD CONSTRAINT FK_188CB665A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA7810A0EA3F FOREIGN KEY (alliance_id) REFERENCES alliance (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA7899E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A657597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE roster ADD CONSTRAINT FK_60B9ADF999E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE roster ADD CONSTRAINT FK_60B9ADF9FA7FD7EB FOREIGN KEY (champion_id) REFERENCES champion (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498F70B6EB FOREIGN KEY (active_player_id) REFERENCES player (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E4410A0EA3F');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA7810A0EA3F');
        $this->addSql('ALTER TABLE roster DROP FOREIGN KEY FK_60B9ADF9FA7FD7EB');
        $this->addSql('ALTER TABLE champion DROP FOREIGN KEY FK_45437EB41136BE75');
        $this->addSql('ALTER TABLE external_character DROP FOREIGN KEY FK_B7211F201136BE75');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A657597D3FE');
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E4499E6F5DF');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA7899E6F5DF');
        $this->addSql('ALTER TABLE roster DROP FOREIGN KEY FK_60B9ADF999E6F5DF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498F70B6EB');
        $this->addSql('ALTER TABLE external_user DROP FOREIGN KEY FK_188CB665A76ED395');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65A76ED395');
        $this->addSql('DROP TABLE alliance');
        $this->addSql('DROP TABLE candidate');
        $this->addSql('DROP TABLE champion');
        $this->addSql('DROP TABLE `character`');
        $this->addSql('DROP TABLE external_character');
        $this->addSql('DROP TABLE external_user');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE roster');
        $this->addSql('DROP TABLE user');
    }
}
