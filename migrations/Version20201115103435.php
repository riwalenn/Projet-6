<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201115103435 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick CHANGE rotation rotation VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD is_active tinyint(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE user DROP status');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick CHANGE rotation rotation INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP roles');
    }
}
