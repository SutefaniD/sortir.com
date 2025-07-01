<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250701131610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '01/07/2025 : correction Participant.phone en string et Modif Status name en enum en PHP et ajout zipCode dans City';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE status CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE participant CHANGE phone phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE city ADD zip_code VARCHAR(6) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE status CHANGE name name VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE participant CHANGE phone phone INT NOT NULL');
        $this->addSql('ALTER TABLE city DROP zip_code');
    }
}
