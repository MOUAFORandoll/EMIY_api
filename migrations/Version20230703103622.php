<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230703103622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD montant INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE commande DROP montant');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
    }
}