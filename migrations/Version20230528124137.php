<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230528124137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE boutique_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE boutique_object_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE commande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE list_commande_livreur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE localisation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE panier_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE produit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE produit_object_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE short_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE boutique (id INT NOT NULL, user_id INT DEFAULT NULL, localisation_id INT DEFAULT NULL, category_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, status BOOLEAN NOT NULL, code_boutique VARCHAR(255) DEFAULT NULL, date_created DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A1223C54A76ED395 ON boutique (user_id)');
        $this->addSql('CREATE INDEX IDX_A1223C54C68BE09C ON boutique (localisation_id)');
        $this->addSql('CREATE INDEX IDX_A1223C5412469DE2 ON boutique (category_id)');
        $this->addSql('CREATE TABLE boutique_object (id INT NOT NULL, boutique_id INT DEFAULT NULL, src VARCHAR(255) NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BB6A4419AB677BE6 ON boutique_object (boutique_id)');
        $this->addSql('CREATE TABLE commande (id INT NOT NULL, mode_paiement_id INT DEFAULT NULL, panier_id INT DEFAULT NULL, localisation_id INT DEFAULT NULL, point_livraison_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(10000) DEFAULT NULL, date_created DATE NOT NULL, code_commande VARCHAR(255) NOT NULL, code_client VARCHAR(255) NOT NULL, status_buy BOOLEAN NOT NULL, status_finish INT NOT NULL, token TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6EEAA67D438F5B63 ON commande (mode_paiement_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DF77D927C ON commande (panier_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DC68BE09C ON commande (localisation_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D78DB0241 ON commande (point_livraison_id)');
        $this->addSql('CREATE TABLE historique_paiement (id INT NOT NULL, user_id INT DEFAULT NULL, type_paiement_id INT DEFAULT NULL, commande_id INT DEFAULT NULL, montant VARCHAR(255) NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, token VARCHAR(255) NOT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_710402ECA76ED395 ON historique_paiement (user_id)');
        $this->addSql('CREATE INDEX IDX_710402EC615593E9 ON historique_paiement (type_paiement_id)');
        $this->addSql('CREATE INDEX IDX_710402EC82EA2E54 ON historique_paiement (commande_id)');
        $this->addSql('CREATE TABLE list_commande_livreur (id INT NOT NULL, commande_id INT DEFAULT NULL, livreur_id INT DEFAULT NULL, date_created DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A629619382EA2E54 ON list_commande_livreur (commande_id)');
        $this->addSql('CREATE INDEX IDX_A6296193F8646701 ON list_commande_livreur (livreur_id)');
        $this->addSql('CREATE TABLE list_produit_panier (id INT NOT NULL, panier_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, status BOOLEAN NOT NULL, quantite INT NOT NULL, code_produit_panier VARCHAR(255) NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AAC86140F77D927C ON list_produit_panier (panier_id)');
        $this->addSql('CREATE INDEX IDX_AAC86140F347EFB ON list_produit_panier (produit_id)');
        $this->addSql('CREATE TABLE panier (id INT NOT NULL, user_id INT DEFAULT NULL, date_created DATE DEFAULT NULL, code_panier VARCHAR(255) NOT NULL, nom_client VARCHAR(255) NOT NULL, prenom_client VARCHAR(255) DEFAULT NULL, phone_client VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_24CC0DF2A76ED395 ON panier (user_id)');
        $this->addSql('CREATE TABLE produit (id INT NOT NULL, category_id INT DEFAULT NULL, boutique_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(10000) DEFAULT NULL, date_created DATE NOT NULL, prix_unitaire INT NOT NULL, quantite INT NOT NULL, status BOOLEAN NOT NULL, code_produit VARCHAR(255) NOT NULL, taille INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29A5EC2712469DE2 ON produit (category_id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27AB677BE6 ON produit (boutique_id)');
        $this->addSql('CREATE TABLE produit_object (id INT NOT NULL, produit_id INT DEFAULT NULL, src TEXT NOT NULL, date_created DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5FFF60D6F347EFB ON produit_object (produit_id)');
        $this->addSql('CREATE TABLE short (id INT NOT NULL, boutique_id INT DEFAULT NULL, src VARCHAR(255) NOT NULL, titre VARCHAR(255) NOT NULL, status BOOLEAN NOT NULL, description VARCHAR(10000) DEFAULT NULL, date_created DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F2890A2AB677BE6 ON short (boutique_id)');
        $this->addSql('CREATE TABLE transaction (id INT NOT NULL, client_id INT DEFAULT NULL, panier_id INT DEFAULT NULL, mode_paiement_id INT DEFAULT NULL, type_transaction_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, montant INT NOT NULL, token VARCHAR(255) NOT NULL, date_create TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status BOOLEAN NOT NULL, nom_client VARCHAR(255) NOT NULL, prenom_client VARCHAR(255) NOT NULL, numero_client VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723705D119EB6921 ON transaction (client_id)');
        $this->addSql('CREATE INDEX IDX_723705D1F77D927C ON transaction (panier_id)');
        $this->addSql('CREATE INDEX IDX_723705D1438F5B63 ON transaction (mode_paiement_id)');
        $this->addSql('CREATE INDEX IDX_723705D17903E29B ON transaction (type_transaction_id)');
        $this->addSql('ALTER TABLE boutique ADD CONSTRAINT FK_A1223C54A76ED395 FOREIGN KEY (user_id) REFERENCES user_plateform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boutique ADD CONSTRAINT FK_A1223C54C68BE09C FOREIGN KEY (localisation_id) REFERENCES localisation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boutique ADD CONSTRAINT FK_A1223C5412469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boutique_object ADD CONSTRAINT FK_BB6A4419AB677BE6 FOREIGN KEY (boutique_id) REFERENCES boutique (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D438F5B63 FOREIGN KEY (mode_paiement_id) REFERENCES mode_paiement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DC68BE09C FOREIGN KEY (localisation_id) REFERENCES localisation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D78DB0241 FOREIGN KEY (point_livraison_id) REFERENCES point_livraison (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE historique_paiement ADD CONSTRAINT FK_710402ECA76ED395 FOREIGN KEY (user_id) REFERENCES user_plateform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE historique_paiement ADD CONSTRAINT FK_710402EC615593E9 FOREIGN KEY (type_paiement_id) REFERENCES type_paiement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE historique_paiement ADD CONSTRAINT FK_710402EC82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE list_commande_livreur ADD CONSTRAINT FK_A629619382EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE list_commande_livreur ADD CONSTRAINT FK_A6296193F8646701 FOREIGN KEY (livreur_id) REFERENCES user_plateform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE list_produit_panier ADD CONSTRAINT FK_AAC86140F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE list_produit_panier ADD CONSTRAINT FK_AAC86140F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2A76ED395 FOREIGN KEY (user_id) REFERENCES user_plateform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27AB677BE6 FOREIGN KEY (boutique_id) REFERENCES boutique (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE produit_object ADD CONSTRAINT FK_5FFF60D6F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE short ADD CONSTRAINT FK_8F2890A2AB677BE6 FOREIGN KEY (boutique_id) REFERENCES boutique (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D119EB6921 FOREIGN KEY (client_id) REFERENCES user_plateform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1438F5B63 FOREIGN KEY (mode_paiement_id) REFERENCES mode_paiement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D17903E29B FOREIGN KEY (type_transaction_id) REFERENCES type_transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE list_produit_promotion ADD CONSTRAINT FK_C9E2B793F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notation_boutique ADD CONSTRAINT FK_7B281E24AB677BE6 FOREIGN KEY (boutique_id) REFERENCES boutique (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notation_produit ADD CONSTRAINT FK_433F4C4CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE notation_boutique DROP CONSTRAINT FK_7B281E24AB677BE6');
        $this->addSql('ALTER TABLE list_produit_promotion DROP CONSTRAINT FK_C9E2B793F347EFB');
        $this->addSql('ALTER TABLE notation_produit DROP CONSTRAINT FK_433F4C4CF347EFB');
        $this->addSql('DROP SEQUENCE boutique_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE boutique_object_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE commande_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE list_commande_livreur_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE localisation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE panier_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE produit_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE produit_object_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE short_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE transaction_id_seq CASCADE');
        $this->addSql('ALTER TABLE boutique DROP CONSTRAINT FK_A1223C54A76ED395');
        $this->addSql('ALTER TABLE boutique DROP CONSTRAINT FK_A1223C54C68BE09C');
        $this->addSql('ALTER TABLE boutique DROP CONSTRAINT FK_A1223C5412469DE2');
        $this->addSql('ALTER TABLE boutique_object DROP CONSTRAINT FK_BB6A4419AB677BE6');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67D438F5B63');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67DF77D927C');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67DC68BE09C');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67D78DB0241');
        $this->addSql('ALTER TABLE historique_paiement DROP CONSTRAINT FK_710402ECA76ED395');
        $this->addSql('ALTER TABLE historique_paiement DROP CONSTRAINT FK_710402EC615593E9');
        $this->addSql('ALTER TABLE historique_paiement DROP CONSTRAINT FK_710402EC82EA2E54');
        $this->addSql('ALTER TABLE list_commande_livreur DROP CONSTRAINT FK_A629619382EA2E54');
        $this->addSql('ALTER TABLE list_commande_livreur DROP CONSTRAINT FK_A6296193F8646701');
        $this->addSql('ALTER TABLE list_produit_panier DROP CONSTRAINT FK_AAC86140F77D927C');
        $this->addSql('ALTER TABLE list_produit_panier DROP CONSTRAINT FK_AAC86140F347EFB');
        $this->addSql('ALTER TABLE panier DROP CONSTRAINT FK_24CC0DF2A76ED395');
        $this->addSql('ALTER TABLE produit DROP CONSTRAINT FK_29A5EC2712469DE2');
        $this->addSql('ALTER TABLE produit DROP CONSTRAINT FK_29A5EC27AB677BE6');
        $this->addSql('ALTER TABLE produit_object DROP CONSTRAINT FK_5FFF60D6F347EFB');
        $this->addSql('ALTER TABLE short DROP CONSTRAINT FK_8F2890A2AB677BE6');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D119EB6921');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1F77D927C');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1438F5B63');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D17903E29B');
        $this->addSql('DROP TABLE boutique');
        $this->addSql('DROP TABLE boutique_object');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE historique_paiement');
        $this->addSql('DROP TABLE list_commande_livreur');
        $this->addSql('DROP TABLE list_produit_panier');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE produit_object');
        $this->addSql('DROP TABLE short');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }
}
