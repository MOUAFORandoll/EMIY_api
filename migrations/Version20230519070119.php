<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230519070119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jwt_refresh_token (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, date_expire_token DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_9F3D9535C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_plateform RENAME INDEX uniq_f140d851444f97dd TO UNIQ_E623D83444F97DD');
        $this->addSql('ALTER TABLE user_plateform RENAME INDEX idx_f140d8518f4fbc60 TO IDX_E623D838F4FBC60');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE jwt_refresh_token');
        $this->addSql('ALTER TABLE user_plateform RENAME INDEX uniq_e623d83444f97dd TO UNIQ_F140D851444F97DD');
        $this->addSql('ALTER TABLE user_plateform RENAME INDEX idx_e623d838f4fbc60 TO IDX_F140D8518F4FBC60');
    }
}
