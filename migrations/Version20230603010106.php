<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230603010106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE message_nogociation_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE message_negociation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE message_negociation (id INT NOT NULL, negociation_id INT DEFAULT NULL, emetteur BOOLEAN NOT NULL, message TEXT NOT NULL, date_envoi TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D59E6921A2D1D3D7 ON message_negociation (negociation_id)');
        $this->addSql('ALTER TABLE message_negociation ADD CONSTRAINT FK_D59E6921A2D1D3D7 FOREIGN KEY (negociation_id) REFERENCES negociation_produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_nogociation DROP CONSTRAINT fk_c2bcf9e8a2d1d3d7');
        $this->addSql('DROP TABLE message_nogociation');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE message_negociation_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE message_nogociation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE message_nogociation (id INT NOT NULL, negociation_id INT DEFAULT NULL, emetteur BOOLEAN NOT NULL, message TEXT NOT NULL, date_envoi TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_c2bcf9e8a2d1d3d7 ON message_nogociation (negociation_id)');
        $this->addSql('ALTER TABLE message_nogociation ADD CONSTRAINT fk_c2bcf9e8a2d1d3d7 FOREIGN KEY (negociation_id) REFERENCES negociation_produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_negociation DROP CONSTRAINT FK_D59E6921A2D1D3D7');
        $this->addSql('DROP TABLE message_negociation');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
    }
}
