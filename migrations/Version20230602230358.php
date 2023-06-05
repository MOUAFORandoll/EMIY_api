<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230602230358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE message_nogociation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE negociation_produit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_commande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE message_nogociation (id INT NOT NULL, negociation_id INT DEFAULT NULL, emetteur BOOLEAN NOT NULL, message TEXT NOT NULL, date_envoi TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C2BCF9E8A2D1D3D7 ON message_nogociation (negociation_id)');
        $this->addSql('CREATE TABLE negociation_produit (id INT NOT NULL, produit_id INT DEFAULT NULL, initiateur_id INT DEFAULT NULL, code_negociation VARCHAR(255) DEFAULT NULL, prix_negocie VARCHAR(255) NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E0EEB46CF347EFB ON negociation_produit (produit_id)');
        $this->addSql('CREATE INDEX IDX_E0EEB46C56D142FC ON negociation_produit (initiateur_id)');
        $this->addSql('CREATE TABLE type_commande (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE message_nogociation ADD CONSTRAINT FK_C2BCF9E8A2D1D3D7 FOREIGN KEY (negociation_id) REFERENCES negociation_produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE negociation_produit ADD CONSTRAINT FK_E0EEB46CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE negociation_produit ADD CONSTRAINT FK_E0EEB46C56D142FC FOREIGN KEY (initiateur_id) REFERENCES user_plateform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT fk_6eeaa67dc68be09c');
        $this->addSql('DROP INDEX idx_6eeaa67dc68be09c');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande RENAME COLUMN localisation_id TO type_commande_id');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DC9F3F9C5 FOREIGN KEY (type_commande_id) REFERENCES type_commande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6EEAA67DC9F3F9C5 ON commande (type_commande_id)');
        $this->addSql('ALTER TABLE list_produit_panier ADD prix_unitaire_vente INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67DC9F3F9C5');
        $this->addSql('DROP SEQUENCE message_nogociation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE negociation_produit_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_commande_id_seq CASCADE');
        $this->addSql('ALTER TABLE message_nogociation DROP CONSTRAINT FK_C2BCF9E8A2D1D3D7');
        $this->addSql('ALTER TABLE negociation_produit DROP CONSTRAINT FK_E0EEB46CF347EFB');
        $this->addSql('ALTER TABLE negociation_produit DROP CONSTRAINT FK_E0EEB46C56D142FC');
        $this->addSql('DROP TABLE message_nogociation');
        $this->addSql('DROP TABLE negociation_produit');
        $this->addSql('DROP TABLE type_commande');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('DROP INDEX IDX_6EEAA67DC9F3F9C5');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande RENAME COLUMN type_commande_id TO localisation_id');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT fk_6eeaa67dc68be09c FOREIGN KEY (localisation_id) REFERENCES localisation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6eeaa67dc68be09c ON commande (localisation_id)');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE list_produit_panier DROP prix_unitaire_vente');
    }
}
