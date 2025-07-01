<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250701115426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ajout zipcode dans City';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE city ADD zip_code VARCHAR(6) DEFAULT NULL');
        $this->addSql('ALTER TABLE location ADD city_id INT NOT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB8BAC62AF ON location (city_id)');
        $this->addSql('ALTER TABLE outing ADD organizer_id INT DEFAULT NULL, CHANGE status_id status_id INT NOT NULL, CHANGE location_id location_id INT NOT NULL');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A10625876C4DDA FOREIGN KEY (organizer_id) REFERENCES participant (id)');
        $this->addSql('CREATE INDEX IDX_F2A10625876C4DDA ON outing (organizer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE city DROP zip_code');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB8BAC62AF');
        $this->addSql('DROP INDEX IDX_5E9E89CB8BAC62AF ON location');
        $this->addSql('ALTER TABLE location DROP city_id');
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A10625876C4DDA');
        $this->addSql('DROP INDEX IDX_F2A10625876C4DDA ON outing');
        $this->addSql('ALTER TABLE outing DROP organizer_id, CHANGE status_id status_id INT DEFAULT NULL, CHANGE location_id location_id INT DEFAULT NULL');
    }
}
