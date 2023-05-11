<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230511202315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE parrainage (id INT AUTO_INCREMENT NOT NULL, parrain_id INT DEFAULT NULL, fieul_id INT DEFAULT NULL, INDEX IDX_195BAFB5DE2A7A37 (parrain_id), INDEX IDX_195BAFB5EC73DAAC (fieul_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE parrainage ADD CONSTRAINT FK_195BAFB5DE2A7A37 FOREIGN KEY (parrain_id) REFERENCES `UserPlateform` (id)');
        $this->addSql('ALTER TABLE parrainage ADD CONSTRAINT FK_195BAFB5EC73DAAC FOREIGN KEY (fieul_id) REFERENCES `UserPlateform` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parrainage DROP FOREIGN KEY FK_195BAFB5DE2A7A37');
        $this->addSql('ALTER TABLE parrainage DROP FOREIGN KEY FK_195BAFB5EC73DAAC');
        $this->addSql('DROP TABLE parrainage');
    }
}
