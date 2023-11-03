<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230920122150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE process (id INT AUTO_INCREMENT NOT NULL, scan_id INT NOT NULL, status VARCHAR(255) NOT NULL, results VARCHAR(255) NOT NULL, INDEX IDX_861D18962827AAD3 (scan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE process ADD CONSTRAINT FK_861D18962827AAD3 FOREIGN KEY (scan_id) REFERENCES scan (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE process DROP FOREIGN KEY FK_861D18962827AAD3');
        $this->addSql('DROP TABLE process');
    }
}
