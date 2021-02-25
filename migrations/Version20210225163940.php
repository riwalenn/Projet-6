<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210225163940 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `trick` ADD `slug` VARCHAR(255) NULL AFTER `user_id`');
        $this->addSql('ALTER TABLE `trick` ADD UNIQUE(`title`)');
        $this->addSql('ALTER TABLE `trick` DROP FOREIGN KEY `FK_D8F0A91EA76ED395`; ALTER TABLE `trick` ADD CONSTRAINT `FK_D8F0A91EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE `comment` DROP FOREIGN KEY `FK_9474526CA76ED395`; ALTER TABLE `comment` ADD CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `comment` DROP FOREIGN KEY `FK_9474526CB281BE2E`; ALTER TABLE `comment` ADD CONSTRAINT `FK_9474526CB281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE `trick_history` DROP FOREIGN KEY `FK_44E70913A76ED395`; ALTER TABLE `trick_history` ADD CONSTRAINT `FK_44E70913A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `trick_history` DROP FOREIGN KEY `FK_44E70913B281BE2E`; ALTER TABLE `trick_history` ADD CONSTRAINT `FK_44E70913B281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE `trick_library` DROP FOREIGN KEY `FK_C2DDE1E4B281BE2E`; ALTER TABLE `trick_library` ADD CONSTRAINT `FK_C2DDE1E4B281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_D8F0A91E989D9B62 ON trick');
        $this->addSql('ALTER TABLE trick DROP slug');
    }
}
