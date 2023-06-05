<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230602233858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE negociation_produit ALTER prix_negocie DROP NOT NULL');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE negociation_produit ALTER prix_negocie SET NOT NULL');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
    }
}
