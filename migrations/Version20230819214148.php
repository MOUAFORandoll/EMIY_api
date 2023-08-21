<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230819214148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE list_produit_short_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE list_produit_short (id INT NOT NULL, produit_id INT DEFAULT NULL, short_id INT DEFAULT NULL, date_created DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_35ABDB3FF347EFB ON list_produit_short (produit_id)');
        $this->addSql('CREATE INDEX IDX_35ABDB3FF8496E51 ON list_produit_short (short_id)');
        $this->addSql('ALTER TABLE list_produit_short ADD CONSTRAINT FK_35ABDB3FF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE list_produit_short ADD CONSTRAINT FK_35ABDB3FF8496E51 FOREIGN KEY (short_id) REFERENCES short (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
      
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE list_produit_short_id_seq CASCADE');
        $this->addSql('ALTER TABLE list_produit_short DROP CONSTRAINT FK_35ABDB3FF347EFB');
        $this->addSql('ALTER TABLE list_produit_short DROP CONSTRAINT FK_35ABDB3FF8496E51');
        $this->addSql('DROP TABLE list_produit_short');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
      
    }
}
