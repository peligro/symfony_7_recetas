<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240809185626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuarios ADD estado_id INT NOT NULL');
        $this->addSql('ALTER TABLE usuarios ADD CONSTRAINT FK_EF687F29F5A440B FOREIGN KEY (estado_id) REFERENCES estado (id)');
        $this->addSql('CREATE INDEX IDX_EF687F29F5A440B ON usuarios (estado_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuarios DROP FOREIGN KEY FK_EF687F29F5A440B');
        $this->addSql('DROP INDEX IDX_EF687F29F5A440B ON usuarios');
        $this->addSql('ALTER TABLE usuarios DROP estado_id');
    }
}
