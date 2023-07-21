<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230721202043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE short_comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE short_like_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE short_comment (id INT NOT NULL, short_id INT DEFAULT NULL, client_id INT DEFAULT NULL, comment TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3FD585C4F8496E51 ON short_comment (short_id)');
        $this->addSql('CREATE INDEX IDX_3FD585C419EB6921 ON short_comment (client_id)');
        $this->addSql('CREATE TABLE short_like (id INT NOT NULL, short_id INT DEFAULT NULL, client_id INT DEFAULT NULL, like_short BOOLEAN NOT NULL, date_created DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CCA48E26F8496E51 ON short_like (short_id)');
        $this->addSql('CREATE INDEX IDX_CCA48E2619EB6921 ON short_like (client_id)');
        $this->addSql('ALTER TABLE short_comment ADD CONSTRAINT FK_3FD585C4F8496E51 FOREIGN KEY (short_id) REFERENCES short (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE short_comment ADD CONSTRAINT FK_3FD585C419EB6921 FOREIGN KEY (client_id) REFERENCES user_plateform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE short_like ADD CONSTRAINT FK_CCA48E26F8496E51 FOREIGN KEY (short_id) REFERENCES short (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE short_like ADD CONSTRAINT FK_CCA48E2619EB6921 FOREIGN KEY (client_id) REFERENCES user_plateform (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');

        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE short_comment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE short_like_id_seq CASCADE');
        $this->addSql('ALTER TABLE short_comment DROP CONSTRAINT FK_3FD585C4F8496E51');
        $this->addSql('ALTER TABLE short_comment DROP CONSTRAINT FK_3FD585C419EB6921');
        $this->addSql('ALTER TABLE short_like DROP CONSTRAINT FK_CCA48E26F8496E51');
        $this->addSql('ALTER TABLE short_like DROP CONSTRAINT FK_CCA48E2619EB6921');
        $this->addSql('DROP TABLE short_comment');
        $this->addSql('DROP TABLE short_like');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE produit_object ALTER src TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');
        $this->addSql('ALTER TABLE commande ALTER token TYPE TEXT');

        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
        $this->addSql('ALTER TABLE user_plateform ALTER key_secret TYPE TEXT');
    }
}
