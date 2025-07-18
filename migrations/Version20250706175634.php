<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250706175634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, zip_code VARCHAR(5) NOT NULL, UNIQUE INDEX UNIQ_2D5B02345E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(250) NOT NULL, street VARCHAR(100) NOT NULL, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, INDEX IDX_5E9E89CB8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outing (id INT AUTO_INCREMENT NOT NULL, organizer_id INT NOT NULL, status_id INT NOT NULL, location_id INT NOT NULL, site_id INT NOT NULL, name VARCHAR(255) NOT NULL, starting_date_time DATETIME NOT NULL, duration INT NOT NULL, registration_deadline DATETIME NOT NULL, max_participants INT NOT NULL, outing_details LONGTEXT NOT NULL, cancel_reason LONGTEXT DEFAULT NULL, INDEX IDX_F2A10625876C4DDA (organizer_id), INDEX IDX_F2A106256BF700BD (status_id), INDEX IDX_F2A1062564D218E (location_id), INDEX IDX_F2A10625F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outing_participant (outing_id INT NOT NULL, participant_id INT NOT NULL, INDEX IDX_1F9F35B7AF4C7531 (outing_id), INDEX IDX_1F9F35B79D1C3019 (participant_id), PRIMARY KEY(outing_id, participant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, last_name VARCHAR(150) NOT NULL, first_name VARCHAR(150) NOT NULL, username VARCHAR(150) NOT NULL, phone VARCHAR(10) NOT NULL, email VARCHAR(150) NOT NULL, password VARCHAR(255) NOT NULL, administrator TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_D79F6B11F85E0677 (username), INDEX IDX_D79F6B11F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A10625876C4DDA FOREIGN KEY (organizer_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A106256BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A1062564D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A10625F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE outing_participant ADD CONSTRAINT FK_1F9F35B7AF4C7531 FOREIGN KEY (outing_id) REFERENCES outing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outing_participant ADD CONSTRAINT FK_1F9F35B79D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB8BAC62AF');
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A10625876C4DDA');
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A106256BF700BD');
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A1062564D218E');
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A10625F6BD1646');
        $this->addSql('ALTER TABLE outing_participant DROP FOREIGN KEY FK_1F9F35B7AF4C7531');
        $this->addSql('ALTER TABLE outing_participant DROP FOREIGN KEY FK_1F9F35B79D1C3019');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11F6BD1646');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE outing');
        $this->addSql('DROP TABLE outing_participant');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
