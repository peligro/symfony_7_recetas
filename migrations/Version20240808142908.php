<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240808142908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE receta (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, nombre VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL, tiempo VARCHAR(50) NOT NULL, detalle LONGTEXT NOT NULL, foto VARCHAR(100) NOT NULL, fecha DATETIME NOT NULL, INDEX IDX_B093494E3397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE receta ADD CONSTRAINT FK_B093494E3397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receta DROP FOREIGN KEY FK_B093494E3397707A');
        $this->addSql('DROP TABLE receta');
    }
}
