<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230806182714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE user_read_short_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_read_short (id INT NOT NULL, short_id INT DEFAULT NULL, client_id INT DEFAULT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_77CF2A9AF8496E51 ON user_read_short (short_id)');
        $this->addSql('CREATE INDEX IDX_77CF2A9A19EB6921 ON user_read_short (client_id)');
        $this->addSql('ALTER TABLE user_read_short ADD CONSTRAINT FK_77CF2A9AF8496E51 FOREIGN KEY (short_id) REFERENCES short (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_read_short ADD CONSTRAINT FK_77CF2A9A19EB6921 FOREIGN KEY (client_id) REFERENCES user_plateform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
         $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_read_short_id_seq CASCADE');
        $this->addSql('ALTER TABLE user_read_short DROP CONSTRAINT FK_77CF2A9AF8496E51');
        $this->addSql('ALTER TABLE user_read_short DROP CONSTRAINT FK_77CF2A9A19EB6921');
        $this->addSql('DROP TABLE user_read_short');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
    }
}
