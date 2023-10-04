<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231004124603 extends AbstractMigration
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
        $this->addSql('ALTER TABLE tool DROP FOREIGN KEY FK_20F33ED1186CAF79');
        $this->addSql('DROP TABLE tool');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tool (id INT AUTO_INCREMENT NOT NULL, scan_id_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, results LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', INDEX IDX_20F33ED1186CAF79 (scan_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tool ADD CONSTRAINT FK_20F33ED1186CAF79 FOREIGN KEY (scan_id_id) REFERENCES scan (id)');
        $this->addSql('ALTER TABLE process DROP FOREIGN KEY FK_861D18962827AAD3');
        $this->addSql('DROP TABLE process');
    }
}
