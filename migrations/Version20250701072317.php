<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250701072317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE outing_participant (outing_id INT NOT NULL, participant_id INT NOT NULL, INDEX IDX_1F9F35B7AF4C7531 (outing_id), INDEX IDX_1F9F35B79D1C3019 (participant_id), PRIMARY KEY(outing_id, participant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE outing_participant ADD CONSTRAINT FK_1F9F35B7AF4C7531 FOREIGN KEY (outing_id) REFERENCES outing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outing_participant ADD CONSTRAINT FK_1F9F35B79D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outing ADD site_id INT DEFAULT NULL, ADD status_id INT DEFAULT NULL, ADD location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A10625F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A106256BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A1062564D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_F2A10625F6BD1646 ON outing (site_id)');
        $this->addSql('CREATE INDEX IDX_F2A106256BF700BD ON outing (status_id)');
        $this->addSql('CREATE INDEX IDX_F2A1062564D218E ON outing (location_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE outing_participant DROP FOREIGN KEY FK_1F9F35B7AF4C7531');
        $this->addSql('ALTER TABLE outing_participant DROP FOREIGN KEY FK_1F9F35B79D1C3019');
        $this->addSql('DROP TABLE outing_participant');
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A10625F6BD1646');
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A106256BF700BD');
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A1062564D218E');
        $this->addSql('DROP INDEX IDX_F2A10625F6BD1646 ON outing');
        $this->addSql('DROP INDEX IDX_F2A106256BF700BD ON outing');
        $this->addSql('DROP INDEX IDX_F2A1062564D218E ON outing');
        $this->addSql('ALTER TABLE outing DROP site_id, DROP status_id, DROP location_id');
    }
}
