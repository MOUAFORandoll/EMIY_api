<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723085028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT'); 
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE short_comment ADD reference_commentaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE short_comment DROP deleted_at');
        $this->addSql('ALTER TABLE short_comment ADD CONSTRAINT FK_3FD585C4E30C14B2 FOREIGN KEY (reference_commentaire_id) REFERENCES short_comment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3FD585C4E30C14B2 ON short_comment (reference_commentaire_id)');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public'); 
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE short_comment DROP CONSTRAINT FK_3FD585C4E30C14B2');
        $this->addSql('DROP INDEX IDX_3FD585C4E30C14B2');
        $this->addSql('ALTER TABLE short_comment ADD deleted_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE short_comment DROP reference_commentaire_id');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }
}
