<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240810131602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receta ADD usuario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE receta ADD CONSTRAINT FK_B093494EDB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id)');
        $this->addSql('CREATE INDEX IDX_B093494EDB38439E ON receta (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receta DROP FOREIGN KEY FK_B093494EDB38439E');
        $this->addSql('DROP INDEX IDX_B093494EDB38439E ON receta');
        $this->addSql('ALTER TABLE receta DROP usuario_id');
    }
}
