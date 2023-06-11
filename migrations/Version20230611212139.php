<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230611212139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE like_produit (id INT NOT NULL, client_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, "like" BOOLEAN NOT NULL, date_created DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7A28A60419EB6921 ON like_produit (client_id)');
        $this->addSql('CREATE INDEX IDX_7A28A604F347EFB ON like_produit (produit_id)');
        $this->addSql('ALTER TABLE like_produit ADD CONSTRAINT FK_7A28A60419EB6921 FOREIGN KEY (client_id) REFERENCES user_plateform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE like_produit ADD CONSTRAINT FK_7A28A604F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE like_produit DROP CONSTRAINT FK_7A28A60419EB6921');
        $this->addSql('ALTER TABLE like_produit DROP CONSTRAINT FK_7A28A604F347EFB');
        $this->addSql('DROP TABLE like_produit');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
    }
}
