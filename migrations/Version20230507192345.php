<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507192345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notation_produit (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, note INT NOT NULL, date_created DATE DEFAULT NULL, INDEX IDX_433F4C4CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notation_produit ADD CONSTRAINT FK_433F4C4CA76ED395 FOREIGN KEY (user_id) REFERENCES `UserPlateform` (id)');
        $this->addSql('DROP INDEX UNIQ_9F3D95358C0735FF ON jwt_refresh_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F3D9535C74F2195 ON jwt_refresh_token (refresh_token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notation_produit DROP FOREIGN KEY FK_433F4C4CA76ED395');
        $this->addSql('DROP TABLE notation_produit');
        $this->addSql('DROP INDEX UNIQ_9F3D9535C74F2195 ON jwt_refresh_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F3D95358C0735FF ON jwt_refresh_token (valid)');
    }
}
