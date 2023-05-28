<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230528124421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boutique (id INT NOT NULL, user_id INT DEFAULT NULL, localisation_id INT DEFAULT NULL, category_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, status BOOLEAN NOT NULL, code_boutique VARCHAR(255) DEFAULT NULL, date_created DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A1223C54A76ED395 ON boutique (user_id)');
        $this->addSql('CREATE INDEX IDX_A1223C54C68BE09C ON boutique (localisation_id)');
        $this->addSql('CREATE INDEX IDX_A1223C5412469DE2 ON boutique (category_id)');
        $this->addSql('CREATE TABLE boutique_object (id INT NOT NULL, boutique_id INT DEFAULT NULL, src VARCHAR(255) NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BB6A4419AB677BE6 ON boutique_object (boutique_id)');
        $this->addSql('CREATE TABLE short (id INT NOT NULL, boutique_id INT DEFAULT NULL, src VARCHAR(255) NOT NULL, titre VARCHAR(255) NOT NULL, status BOOLEAN NOT NULL, description VARCHAR(10000) DEFAULT NULL, date_created DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F2890A2AB677BE6 ON short (boutique_id)');
        $this->addSql('ALTER TABLE boutique ADD CONSTRAINT FK_A1223C54A76ED395 FOREIGN KEY (user_id) REFERENCES user_plateform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boutique ADD CONSTRAINT FK_A1223C54C68BE09C FOREIGN KEY (localisation_id) REFERENCES localisation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boutique ADD CONSTRAINT FK_A1223C5412469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boutique_object ADD CONSTRAINT FK_BB6A4419AB677BE6 FOREIGN KEY (boutique_id) REFERENCES boutique (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE short ADD CONSTRAINT FK_8F2890A2AB677BE6 FOREIGN KEY (boutique_id) REFERENCES boutique (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE notation_boutique ADD CONSTRAINT FK_7B281E24AB677BE6 FOREIGN KEY (boutique_id) REFERENCES boutique (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27AB677BE6 FOREIGN KEY (boutique_id) REFERENCES boutique (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE notation_boutique DROP CONSTRAINT FK_7B281E24AB677BE6');
        $this->addSql('ALTER TABLE produit DROP CONSTRAINT FK_29A5EC27AB677BE6');
        $this->addSql('ALTER TABLE boutique DROP CONSTRAINT FK_A1223C54A76ED395');
        $this->addSql('ALTER TABLE boutique DROP CONSTRAINT FK_A1223C54C68BE09C');
        $this->addSql('ALTER TABLE boutique DROP CONSTRAINT FK_A1223C5412469DE2');
        $this->addSql('ALTER TABLE boutique_object DROP CONSTRAINT FK_BB6A4419AB677BE6');
        $this->addSql('ALTER TABLE short DROP CONSTRAINT FK_8F2890A2AB677BE6');
        $this->addSql('DROP TABLE boutique');
        $this->addSql('DROP TABLE boutique_object');
        $this->addSql('DROP TABLE short');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
    }
}
