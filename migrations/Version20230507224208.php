<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507224208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notation_boutique (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, boutique_id INT DEFAULT NULL, note DOUBLE PRECISION NOT NULL, date_created DATE NOT NULL, INDEX IDX_7B281E2419EB6921 (client_id), INDEX IDX_7B281E24AB677BE6 (boutique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notation_boutique ADD CONSTRAINT FK_7B281E2419EB6921 FOREIGN KEY (client_id) REFERENCES `UserPlateform` (id)');
        $this->addSql('ALTER TABLE notation_boutique ADD CONSTRAINT FK_7B281E24AB677BE6 FOREIGN KEY (boutique_id) REFERENCES boutique (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notation_boutique DROP FOREIGN KEY FK_7B281E2419EB6921');
        $this->addSql('ALTER TABLE notation_boutique DROP FOREIGN KEY FK_7B281E24AB677BE6');
        $this->addSql('DROP TABLE notation_boutique');
    }
}
