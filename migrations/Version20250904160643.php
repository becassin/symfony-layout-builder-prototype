<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250904160643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE layout (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, entity_type VARCHAR(255) DEFAULT NULL, entity_id INT DEFAULT NULL, is_default TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE layout_block (id INT AUTO_INCREMENT NOT NULL, layout_id INT NOT NULL, block_type VARCHAR(255) NOT NULL, configuration JSON NOT NULL COMMENT \'(DC2Type:json)\', position INT NOT NULL, region VARCHAR(255) DEFAULT NULL, INDEX IDX_A27FFE88C22AA1A (layout_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE layout_block ADD CONSTRAINT FK_A27FFE88C22AA1A FOREIGN KEY (layout_id) REFERENCES layout (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE layout_block DROP FOREIGN KEY FK_A27FFE88C22AA1A');
        $this->addSql('DROP TABLE layout');
        $this->addSql('DROP TABLE layout_block');
    }
}
