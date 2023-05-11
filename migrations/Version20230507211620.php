<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507211620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE jwt_refresh_token');
        $this->addSql('ALTER TABLE notation_produit DROP FOREIGN KEY FK_433F4C4CA76ED395');
        $this->addSql('DROP INDEX IDX_433F4C4CA76ED395 ON notation_produit');
        $this->addSql('ALTER TABLE notation_produit ADD produit_id INT DEFAULT NULL, ADD `float` VARCHAR(255) NOT NULL, ADD date VARCHAR(255) NOT NULL, DROP note, DROP date_created, CHANGE id id VARCHAR(255) NOT NULL, CHANGE user_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notation_produit ADD CONSTRAINT FK_433F4C4C19EB6921 FOREIGN KEY (client_id) REFERENCES `UserPlateform` (id)');
        $this->addSql('ALTER TABLE notation_produit ADD CONSTRAINT FK_433F4C4CF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_433F4C4C19EB6921 ON notation_produit (client_id)');
        $this->addSql('CREATE INDEX IDX_433F4C4CF347EFB ON notation_produit (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE jwt_refresh_token (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, username VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, valid DATETIME NOT NULL, date_expire_token DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_9F3D9535C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE notation_produit DROP FOREIGN KEY FK_433F4C4C19EB6921');
        $this->addSql('ALTER TABLE notation_produit DROP FOREIGN KEY FK_433F4C4CF347EFB');
        $this->addSql('DROP INDEX IDX_433F4C4C19EB6921 ON notation_produit');
        $this->addSql('DROP INDEX IDX_433F4C4CF347EFB ON notation_produit');
        $this->addSql('ALTER TABLE notation_produit ADD user_id INT DEFAULT NULL, ADD note INT NOT NULL, ADD date_created DATE DEFAULT NULL, DROP client_id, DROP produit_id, DROP `float`, DROP date, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE notation_produit ADD CONSTRAINT FK_433F4C4CA76ED395 FOREIGN KEY (user_id) REFERENCES userplateform (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_433F4C4CA76ED395 ON notation_produit (user_id)');
    }
}
